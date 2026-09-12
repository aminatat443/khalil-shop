<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Page catalogue d'une catégorie, avec filtres et tri (sections 20 à 22 du cahier des charges).
     */
    public function show(Request $request, Category $category): View|JsonResponse
    {
        $subCategories = $category->children()->orderBy('name')->get(['id', 'name']);
        $categoryIds = $subCategories->pluck('id')->push($category->id);

        $query = Product::query()
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->withCount('variants')
            ->withRatings();

        // Filtre "Catégorie" — n'a de sens que sur une page univers (qui a des sous-catégories) ;
        // limité à celles-ci pour ne jamais élargir la portée au-delà de l'univers courant.
        if ($request->filled('categories') && $subCategories->isNotEmpty()) {
            $selected = collect($request->input('categories'))->map(fn ($id) => (int) $id)->intersect($subCategories->pluck('id'));

            if ($selected->isNotEmpty()) {
                $query->whereIn('category_id', $selected);
            }
        }

        if ($request->filled('prix_min')) {
            $query->where('price', '>=', (int) $request->input('prix_min'));
        }

        if ($request->filled('prix_max')) {
            $query->where('price', '<=', (int) $request->input('prix_max'));
        }

        if ($request->boolean('nouveautes')) {
            $query->where('is_new', true);
        }

        if ($request->boolean('promotions')) {
            $query->where('is_promo', true);
        }

        match ($request->input('tri')) {
            'prix_croissant' => $query->orderBy('price'),
            'prix_decroissant' => $query->orderByDesc('price'),
            'nouveautes' => $query->orderByDesc('is_new')->latest(),
            default => $query->latest(), // "pertinence" par défaut, faute de moteur de recherche pondéré
        };

        $products = $query->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('products.partials.results', ['products' => $products])->render(),
                'count' => $products->total(),
            ]);
        }

        return view('products.index', [
            'category' => $category,
            'subCategories' => $subCategories,
            'products' => $products,
            'priceFloor' => 0,
            'priceCeil' => 500000,
        ]);
    }
}
