<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductService
{
    /**
     * Stock disponible pour un produit, éventuellement pour une variante précise (docs/SPEC.md §4.1).
     * - Une variante précise -> son propre stock.
     * - Un produit avec variantes mais sans sélection -> somme des stocks de toutes les variantes.
     * - Un produit sans variante -> son propre champ `stock`.
     */
    public function availableStock(Product $product, ?ProductVariant $variant = null): int
    {
        if ($variant !== null) {
            return $variant->stock;
        }

        if ($product->variants()->exists()) {
            return (int) $product->variants()->sum('stock');
        }

        return $product->stock;
    }

    public function isInStock(Product $product, ?ProductVariant $variant = null): bool
    {
        return $this->availableStock($product, $variant) > 0;
    }
}
