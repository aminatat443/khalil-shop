<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Gestion des images produit (section 49 du cahier des charges).
     *
     * Cloudinary n'est pas encore configuré (identifiants absents, docs/SPEC.md) — les images
     * sont stockées localement (disque `public`) en attendant. Le champ `public_id` reste vide ;
     * il sera renseigné automatiquement le jour où l'upload Cloudinary est branché ici.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('image')->store('products', 'public');

        $product->images()->create([
            'url' => Storage::disk('public')->url($path),
            'alt' => $request->input('alt') ?: $product->name,
            'sort_order' => $product->images()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Image ajoutée.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        $this->authorize('update', $product);

        // Ne supprime le fichier local que s'il vient bien de notre stockage (pas une URL externe/Cloudinary)
        if (str_contains($image->url, '/storage/products/')) {
            Storage::disk('public')->delete('products/'.basename($image->url));
        }

        $image->delete();

        return back()->with('status', 'Image supprimée.');
    }
}
