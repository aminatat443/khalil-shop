<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    /**
     * Facture / reçu de commande PDF (docs/SPEC.md §2.5) — accessible au client propriétaire
     * de la commande, à l'équipe (Gestionnaire/Admin/Super Admin), ou juste après une commande
     * invité sans compte (même règle que CheckoutController::confirmation).
     */
    public function show(Request $request, Order $order): Response
    {
        abort_unless(
            (auth()->check() && (auth()->user()->isGestionnaire() || $order->user_id === auth()->id()))
                || (! $order->user_id && $request->session()->get('last_order_id') === $order->id),
            403
        );

        $order->load('items', 'coupon');

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->stream('facture-'.$order->order_number.'.pdf');
    }
}
