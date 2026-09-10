<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Models\User;
use App\Notifications\NewReturnNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ReturnController extends Controller
{
    /**
     * Retours et remboursements (docs/SPEC.md §2.1 — 7 jours après réception).
     */
    public function index(): View
    {
        $returns = ProductReturn::whereHas('orderItem.order', fn ($q) => $q->where('user_id', auth()->id()))
            ->with('orderItem.order')
            ->latest()
            ->get();

        return view('account.returns', ['returns' => $returns]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_item_id' => ['required', 'integer', 'exists:order_items,id'],
            'reason' => ['required', 'in:taille,defaut,description,autre'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $orderItem = OrderItem::with('order')->findOrFail($data['order_item_id']);

        abort_unless($orderItem->order->user_id === auth()->id(), 403);
        abort_if($orderItem->order->status === 'annulee', 422, 'Impossible de demander un retour pour une commande annulée.');

        // Fenêtre de 7 jours à partir de la date de la commande.
        abort_if($orderItem->order->created_at->diffInDays(now()) > 7, 422, 'Le délai de retour de 7 jours est dépassé.');

        if ($orderItem->returns()->exists()) {
            return back()->withErrors(['order_item_id' => 'Une demande de retour existe déjà pour cet article.']);
        }

        $return = $orderItem->returns()->create([
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => 'demandee',
        ]);

        Notification::send(User::staff()->get(), new NewReturnNotification($return->load('orderItem.order')));

        return back()->with('status', 'Votre demande de retour a bien été enregistrée.');
    }

    /**
     * Raccourci "un motif pour toute la commande", déclenché depuis la modale de la liste des
     * commandes (compte client) : crée une demande de retour pour chaque article qui n'en a pas
     * déjà une, avec le même motif — plus simple que de traiter chaque article séparément.
     */
    public function storeForOrder(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_if($order->status === 'annulee', 422, 'Impossible de demander un retour pour une commande annulée.');
        abort_if($order->created_at->diffInDays(now()) > 7, 422, 'Le délai de retour de 7 jours est dépassé.');

        $data = $request->validate([
            'reason' => ['required', 'in:taille,defaut,description,autre'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->load('items.returns');

        if ($order->hasAnyReturn()) {
            return back()->withErrors(['reason' => 'Une demande de retour existe déjà pour cette commande.']);
        }

        $firstReturn = null;
        foreach ($order->items as $item) {
            $return = $item->returns()->create([
                'reason' => $data['reason'],
                'description' => $data['description'] ?? null,
                'status' => 'demandee',
            ]);
            $firstReturn ??= $return;
        }

        if ($firstReturn) {
            Notification::send(User::staff()->get(), new NewReturnNotification($firstReturn->load('orderItem.order')));
        }

        return back()->with('status', 'Votre demande de retour a bien été enregistrée.');
    }
}
