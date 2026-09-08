<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\ProductReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        abort_unless($orderItem->order->status === 'livree', 422, 'Le retour n\'est possible que pour une commande livrée.');

        // Fenêtre de 7 jours après livraison (docs/SPEC.md §2.1). La date de livraison exacte
        // n'est pas encore tracée séparément — on approxime avec la dernière mise à jour de la
        // commande, qui correspond au passage au statut "livree" dans le flux actuel.
        abort_if($orderItem->order->updated_at->diffInDays(now()) > 7, 422, 'Le délai de retour de 7 jours est dépassé.');

        if ($orderItem->returns()->exists()) {
            return back()->withErrors(['order_item_id' => 'Une demande de retour existe déjà pour cet article.']);
        }

        $orderItem->returns()->create([
            'reason' => $data['reason'],
            'description' => $data['description'] ?? null,
            'status' => 'demandee',
        ]);

        return back()->with('status', 'Votre demande de retour a bien été enregistrée.');
    }
}
