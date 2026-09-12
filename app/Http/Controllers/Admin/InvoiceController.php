<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Registre des factures — une facture par commande (même PDF que celui du client, généré
     * à la volée par InvoiceController@show, section 2.5 de docs/SPEC.md). Cette vue sert de
     * registre comptable : recherche, filtre par période, total de la période affichée.
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::query()
            ->when($request->filled('q'), fn ($q) => $q->where('order_number', 'ilike', '%'.$request->input('q').'%')
                ->orWhere('customer_name', 'ilike', '%'.$request->input('q').'%'))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));

        $periodTotal = (clone $query)->sum('total');

        $orders = $query->latest()->paginate(25)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.invoices.partials.table', ['orders' => $orders, 'periodTotal' => $periodTotal])->render(),
            ]);
        }

        return view('admin.invoices.index', ['orders' => $orders, 'periodTotal' => $periodTotal]);
    }
}
