<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Gestion des images produit (section 49 du cahier des charges).
     *
     * Bascule automatiquement sur Cloudinary dès que ses identifiants sont renseignés dans
     * .env (Setting::mediaDisk()), stockage local (disque `public`) en attendant.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:4096'],
        ]);

        $disk = Setting::mediaDisk();
        $nextOrder = $product->images()->max('sort_order') + 1;

        foreach ($request->file('images') as $file) {
            $path = $file->store('products', $disk);

            $product->images()->create([
                'url' => Storage::disk($disk)->url($path),
                'public_id' => $disk === 'cloudinary' ? $path : null,
                'alt' => $product->name,
                'sort_order' => $nextOrder++,
            ]);
        }

        return back()->with('status', 'Image(s) ajoutée(s).');
    }

    /**
     * Photo principale : convention "plus petit sort_order" (déjà utilisée par tous les
     * `->first()` sur la relation images dans les contrôleurs vitrine), donc on fait passer
     * l'image choisie devant les autres sans introduire de colonne dédiée.
     */
    public function primary(Product $product, ProductImage $image): RedirectResponse
    {
        $this->authorize('update', $product);

        abort_unless($image->product_id === $product->id, 404);

        $product->images()->where('id', '!=', $image->id)->increment('sort_order');
        $image->update(['sort_order' => 0]);

        return back()->with('status', 'Photo principale mise à jour.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        $this->authorize('update', $product);

        if ($image->public_id) {
            Storage::disk('cloudinary')->delete($image->public_id);
        } elseif (str_contains($image->url, '/storage/products/')) {
            // Ne supprime le fichier local que s'il vient bien de notre stockage (pas une URL externe)
            Storage::disk('public')->delete('products/'.basename($image->url));
        }

        $image->delete();

        return back()->with('status', 'Image supprimée.');
    }
}
