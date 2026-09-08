<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Avis clients (section 39 du cahier des charges) — notation 1-5 + commentaire,
     * soumis à modération avant publication.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->reviews()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null, 'is_approved' => false]
        );

        return back()->with('status', 'Merci pour votre avis ! Il sera visible après modération.');
    }
}
