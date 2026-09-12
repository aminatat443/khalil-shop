<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    /**
     * Gestion des commandes (section 44 du cahier des charges).
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->when($request->filled('q'), fn ($q) => $q->where('order_number', 'ilike', '%'.$request->input('q').'%')
                ->orWhere('customer_name', 'ilike', '%'.$request->input('q').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.partials.table', ['orders' => $orders])->render(),
            ]);
        }

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load('items', 'user', 'coupon');

        return view('admin.orders.show', ['order' => $order]);
    }

    /**
     * Formulaire de vente conclue directement en boutique (section "commande sur place").
     */
    public function create(): View
    {
        $this->authorize('create', Order::class);

        $products = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order'), 'variants.color', 'variants.size'])
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'image' => $product->images->first()?->url,
                'variants' => $product->variants->map(fn ($variant) => [
                    'id' => $variant->id,
                    'label' => collect([$variant->color?->name, $variant->size?->name])->filter()->implode(' / ') ?: 'Variante',
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                ]),
            ]);

        return view('admin.orders.create', ['products' => $products]);
    }

    /**
     * Recherche en temps réel d'un client déjà inscrit, pour préremplir le formulaire de vente
     * en boutique sans ressaisir ses coordonnées (section "commande sur place").
     */
    public function searchClients(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $query = trim((string) $request->input('q'));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = User::query()
            ->where('role', Role::Client)
            ->where(fn ($q) => $q->where('name', 'ilike', "%{$query}%")->orWhere('email', 'ilike', "%{$query}%"))
            ->with(['addresses' => fn ($q) => $q->where('is_default', true)])
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->addresses->first()?->phone ?? '',
            ]);

        return response()->json($clients);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Order::class);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id' => [
                'nullable', 'integer', 'exists:product_variants,id',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $productId = $request->input("items.{$index}.product_id");

                    if (! $value && $productId && Product::find($productId)?->variants()->exists()) {
                        $fail('Choisissez une taille/couleur pour ce produit.');
                    }

                    if ($value && $productId) {
                        $variant = \App\Models\ProductVariant::find($value);
                        if ($variant && $variant->product_id !== (int) $productId) {
                            $fail('La variante sélectionnée ne correspond pas au produit.');
                        }
                    }
                },
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $order = $this->orders->createInStore(
                ['name' => $data['customer_name'], 'phone' => $data['customer_phone'], 'email' => $data['customer_email'] ?? null, 'user_id' => $data['user_id'] ?? null],
                $data['items'],
            );
        } catch (Throwable $e) {
            return back()->withInput()->withErrors(['items' => 'Impossible d\'enregistrer la vente : '.$e->getMessage()]);
        }

        $success = $this->orders->confirm($order);

        return redirect()->route('admin.orders.show', $order)->with('status', $success
            ? 'Vente enregistrée et stock mis à jour.'
            : 'Vente enregistrée, mais rupture de stock détectée — vérifiez la commande.');
    }

    /**
     * Confirmation manuelle (paiement à la livraison) : décrément atomique du stock
     * (docs/SPEC.md §2.3/§2.4).
     */
    public function confirm(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $success = $this->orders->confirm($order);

        $this->notifyStatus($order);

        return back()->with('status', $success
            ? 'Commande confirmée, stock mis à jour.'
            : 'Rupture de stock détectée — la commande a été annulée automatiquement.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->orders->cancel($order);

        $this->notifyStatus($order);

        return back()->with('status', 'Commande annulée, stock restauré si nécessaire.');
    }

    /**
     * Changement de statut logistique (en préparation / expédiée / livrée) — sans impact sur le stock.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'status' => ['required', 'in:en_preparation,expediee,livree'],
        ]);

        $order->update(['status' => $data['status']]);

        $this->notifyStatus($order);

        return back()->with('status', 'Statut mis à jour.');
    }

    /**
     * Email de suivi envoyé au client à chaque changement de statut — l'échec d'envoi (Brevo
     * indisponible, etc.) ne doit jamais faire échouer l'action admin déjà appliquée en base.
     */
    private function notifyStatus(Order $order): void
    {
        $order = $order->fresh();

        try {
            Mail::to($order->customer_email)->send(new OrderStatusMail($order));
        } catch (Throwable $e) {
            report($e);
        }

        // Notification en app — seulement si la commande est rattachée à un compte (une
        // commande invité n'a personne à notifier côté client).
        $order->user?->notify(new OrderStatusNotification($order));
    }
}
