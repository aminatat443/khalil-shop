<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Page catalogue d'une catégorie, avec filtres et tri (sections 20 à 22 du cahier des charges).
     */
    public function show(Request $request, Category $category): View
    {
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        $query = Product::query()
            ->where('is_active', true)
            ->whereIn('category_id', $categoryIds)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->withCount('variants');

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

        return view('products.index', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
