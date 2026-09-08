<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart)
    {
    }

    /**
     * Panier (section 33 du cahier des charges). Contenu rendu côté client depuis le store
     * Alpine ($store.cart), déjà hydraté par le composer du header sur chaque page.
     */
    public function index(): View
    {
        return view('cart.index');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $variant = isset($data['variant_id']) ? ProductVariant::find($data['variant_id']) : null;

        $this->cart->add($product, $variant, $data['quantity'] ?? 1);

        if ($request->wantsJson()) {
            return response()->json($this->cart->summary());
        }

        return back()->with('status', 'Produit ajouté au panier.');
    }

    /**
     * Choix de la taille/couleur dans le panier, pour un article ajouté sans variante
     * (la sélection se fait au moment de la commande plutôt qu'à l'ajout).
     */
    public function assignVariant(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
        ]);

        $this->cart->assignVariant($data['product_id'], $data['variant_id']);

        return response()->json($this->cart->summary());
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
        ]);

        $this->cart->remove($data['product_id'], $data['variant_id'] ?? null);

        return response()->json($this->cart->summary());
    }
}
