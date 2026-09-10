<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReturnStatusMail;
use App\Models\ProductReturn;
use App\Notifications\ReturnStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ReturnController extends Controller
{
    /**
     * Traitement des demandes de retour (docs/SPEC.md §2.1).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductReturn::class);

        $returns = ProductReturn::with('orderItem.order')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->input('q').'%';
                $query->where(function ($q) use ($term) {
                    $q->where('description', 'ilike', $term)
                        ->orWhereHas('orderItem', fn ($q) => $q->where('product_name', 'ilike', $term))
                        ->orWhereHas('orderItem.order', fn ($q) => $q->where('order_number', 'ilike', $term));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.returns.index', ['returns' => $returns]);
    }

    public function update(Request $request, ProductReturn $return): RedirectResponse
    {
        $this->authorize('update', $return);

        $data = $request->validate([
            'status' => ['required', 'in:demandee,acceptee,refusee,article_recu,remboursee'],
            'refund_method' => ['nullable', 'in:credit,moyen_original'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $return->update($data);

        $return = $return->fresh(['orderItem.order']);

        try {
            Mail::to($return->orderItem->order->customer_email)->send(new ReturnStatusMail($return));
        } catch (Throwable $e) {
            report($e);
        }

        $return->orderItem->order->user?->notify(new ReturnStatusNotification($return));

        return back()->with('status', 'Retour mis à jour.');
    }
}
