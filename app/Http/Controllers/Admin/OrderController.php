<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    /**
     * Gestion des commandes (section 44 du cahier des charges).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->when($request->filled('q'), fn ($q) => $q->where('order_number', 'ilike', '%'.$request->input('q').'%')
                ->orWhere('customer_name', 'ilike', '%'.$request->input('q').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load('items', 'user', 'coupon');

        return view('admin.orders.show', ['order' => $order]);
    }

    /**
     * Confirmation manuelle (paiement à la livraison) : décrément atomique du stock
     * (docs/SPEC.md §2.3/§2.4).
     */
    public function confirm(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $success = $this->orders->confirm($order);

        return back()->with('status', $success
            ? 'Commande confirmée, stock mis à jour.'
            : 'Rupture de stock détectée — la commande a été annulée automatiquement.');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('cancel', $order);

        $this->orders->cancel($order);

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

        return back()->with('status', 'Statut mis à jour.');
    }
}
