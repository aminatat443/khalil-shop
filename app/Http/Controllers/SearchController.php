<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Recherche produit (section 10) + points d'entrée "Nouveautés"/"Promotions" du header (section 9).
     */
    public function index(Request $request): View
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->withCount('variants');

        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->input('q').'%');
        }

        if ($request->boolean('nouveautes')) {
            $query->where('is_new', true);
        }

        if ($request->boolean('promotions')) {
            $query->where('is_promo', true);
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('products.search', [
            'products' => $products,
            'query' => $request->input('q'),
        ]);
    }

    /**
     * Suggestions de recherche en temps réel (barre de recherche du header) — quelques
     * résultats affichés au fil de la frappe, avant validation vers la page complète.
     */
    public function suggest(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q'));

        if ($term === '') {
            return response()->json(['products' => []]);
        }

        $products = Product::query()
            ->where('is_active', true)
            ->where('name', 'ilike', '%'.$term.'%')
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->images->first()?->url,
                'url' => route('products.show', $product),
            ]);

        return response()->json(['products' => $products]);
    }
}
