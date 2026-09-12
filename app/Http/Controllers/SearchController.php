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
    public function index(Request $request): View|JsonResponse
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->withCount('variants')
            ->withRatings();

        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->input('q').'%');
        }

        if ($request->boolean('nouveautes')) {
            $query->where('is_new', true);
        }

        if ($request->boolean('promotions')) {
            $query->where('is_promo', true);
        }

        if ($request->filled('prix_min')) {
            $query->where('price', '>=', (int) $request->input('prix_min'));
        }

        if ($request->filled('prix_max')) {
            $query->where('price', '<=', (int) $request->input('prix_max'));
        }

        match ($request->input('tri')) {
            'prix_croissant' => $query->orderBy('price'),
            'prix_decroissant' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.search-results', ['products' => $products])->render(),
                'count' => $products->total(),
            ]);
        }

        return view('products.search', [
            'products' => $products,
            'query' => $request->input('q'),
            'priceFloor' => 0,
            'priceCeil' => 500000,
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
