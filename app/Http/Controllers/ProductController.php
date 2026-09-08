<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Services\PromotionService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly PromotionService $promotions,
    ) {
    }

    /**
     * Fiche produit (sections 25 à 29 du cahier des charges).
     */
    public function show(Product $product): View
    {
        $product->load(['images', 'variants.color', 'variants.size', 'category']);

        $colors = $product->variants->pluck('color')->filter()->unique('id')->values();
        $sizes = $product->variants->pluck('size')->filter()->unique('id')->values();

        $reviews = $product->reviews()->where('is_approved', true)->with('user')->latest()->get();
        $myReview = auth()->check() ? $product->reviews()->where('user_id', auth()->id())->first() : null;

        return view('products.show', [
            'product' => $product,
            'colors' => $colors,
            'sizes' => $sizes,
            'effectivePrice' => $this->promotions->effectivePrice($product),
            'inStock' => $this->products->isInStock($product),
            'reviews' => $reviews,
            'averageRating' => $reviews->isNotEmpty() ? round($reviews->avg('rating'), 1) : null,
            'myReview' => $myReview,
        ]);
    }
}
