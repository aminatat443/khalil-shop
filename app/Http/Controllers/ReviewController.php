<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Avis clients (section 39 du cahier des charges) — notation 1-5 + commentaire,
     * soumis à modération avant publication. Modifiable par son auteur pendant 30 minutes
     * seulement (voir ReviewPolicy::update) pour éviter qu'un avis change après coup.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = $product->reviews()->where('user_id', auth()->id())->first();

        if ($existing && $request->user()->cannot('update', $existing)) {
            return back()->with('error', 'Le délai de 30 minutes pour modifier votre avis est dépassé.');
        }

        $product->reviews()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null, 'is_approved' => false]
        );

        return back()->with('status', 'Merci pour votre avis ! Il sera visible après modération.');
    }

    /**
     * Suppression de son propre avis (ou par un gestionnaire), sans limite de temps.
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $review = $product->reviews()->where('user_id', auth()->id())->firstOrFail();

        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('status', 'Votre avis a été supprimé.');
    }
}
