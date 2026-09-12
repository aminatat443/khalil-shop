<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    /**
     * Génération en masse des variantes (sections 27, 41, 45 du cahier des charges) : l'admin
     * coche plusieurs couleurs et/ou tailles, une case de stock apparaît pour chaque
     * combinaison — plus besoin de répéter "choisir couleur + taille + SKU + Ajouter" une
     * par une, et le stock de départ peut différer d'une combinaison à l'autre.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'variants.*.size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
        ]);

        $colorNames = Color::pluck('name', 'id');
        $sizeNames = Size::pluck('name', 'id');

        $created = 0;
        $skipped = 0;

        foreach ($data['variants'] as $row) {
            $colorId = $row['color_id'] ?? null;
            $sizeId = $row['size_id'] ?? null;

            if ($product->variants()->where('color_id', $colorId)->where('size_id', $sizeId)->exists()) {
                $skipped++;

                continue;
            }

            $skuParts = [Str::upper($product->slug)];
            if ($sizeId) {
                $skuParts[] = Str::upper($sizeNames[$sizeId]);
            }
            if ($colorId) {
                $skuParts[] = Str::upper($colorNames[$colorId]);
            }

            $sku = $baseSku = implode('-', $skuParts);
            $suffix = 1;
            while (ProductVariant::where('sku', $sku)->exists()) {
                $sku = $baseSku.'-'.(++$suffix);
            }

            $product->variants()->create([
                'color_id' => $colorId,
                'size_id' => $sizeId,
                'stock' => $row['stock'],
                'sku' => $sku,
            ]);
            $created++;
        }

        $message = $created > 0 ? "{$created} variante(s) créée(s)." : 'Aucune nouvelle variante — toutes ces combinaisons existent déjà.';
        if ($skipped > 0) {
            $message .= " {$skipped} déjà existante(s) ignorée(s).";
        }

        return back()->with('status', $message);
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $request->validate([
            'color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku,'.$variant->id],
        ]);

        $variant->update($data);

        return back()->with('status', 'Variante mise à jour.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $this->authorize('update', $product);

        $variant->delete();

        return back()->with('status', 'Variante supprimée.');
    }
}
