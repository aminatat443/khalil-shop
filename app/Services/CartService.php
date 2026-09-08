<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private readonly PromotionService $promotions)
    {
    }

    public function add(Product $product, ?ProductVariant $variant, int $quantity = 1): void
    {
        $key = $this->itemKey($product->id, $variant?->id);
        $cart = $this->raw();
        $cart[$key] = ($cart[$key] ?? 0) + $quantity;
        Session::put(self::SESSION_KEY, $cart);
    }

    public function updateQuantity(int $productId, ?int $variantId, int $quantity): void
    {
        $key = $this->itemKey($productId, $variantId);
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key] = $quantity;
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productId, ?int $variantId): void
    {
        $this->updateQuantity($productId, $variantId, 0);
    }

    /**
     * Affecte une variante (taille/couleur) à une ligne du panier ajoutée sans variante —
     * le client choisit au moment de la commande plutôt qu'à l'ajout au panier.
     */
    public function assignVariant(int $productId, int $variantId): void
    {
        $cart = $this->raw();
        $oldKey = $this->itemKey($productId, null);
        $quantity = $cart[$oldKey] ?? 1;
        unset($cart[$oldKey]);

        $newKey = $this->itemKey($productId, $variantId);
        $cart[$newKey] = ($cart[$newKey] ?? 0) + $quantity;

        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Hydrate le panier en objets exploitables (produit, variante, quantité, prix, sous-total).
     * Le prix est toujours recalculé depuis la base — jamais fait confiance à une valeur envoyée
     * par le client (cohérent avec la section 58 du cahier des charges — validation serveur).
     *
     * @return list<array{product: Product, variant: ?ProductVariant, quantity: int, unit_price: int, subtotal: int}>
     */
    public function items(): array
    {
        $items = [];

        foreach ($this->raw() as $key => $quantity) {
            [$productId, $variantId] = $this->parseKey($key);

            $product = Product::find($productId);
            if (! $product) {
                continue; // produit supprimé depuis l'ajout au panier
            }

            $variant = $variantId ? ProductVariant::find($variantId) : null;
            $unitPrice = $this->promotions->effectivePrice($product, $variant);

            $items[] = [
                'product' => $product,
                'variant' => $variant,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
            ];
        }

        return $items;
    }

    public function subtotal(): int
    {
        return array_sum(array_column($this->items(), 'subtotal'));
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Représentation JSON-friendly du panier, pour le tiroir flottant mis à jour sans
     * rechargement de page (fetch + Alpine store côté client).
     */
    public function summary(): array
    {
        $items = $this->items();

        return [
            'items' => array_map(function (array $item) {
                $needsVariant = is_null($item['variant']) && $item['product']->variants()->exists();

                return [
                    'product_id' => $item['product']->id,
                    'variant_id' => $item['variant']?->id,
                    'needs_variant' => $needsVariant,
                    'variant_options' => $needsVariant ? $this->variantOptions($item['product']) : null,
                    'name' => $item['product']->name,
                    'image' => $item['product']->images->first()?->url,
                    'variant_label' => $item['variant']
                        ? collect([$item['variant']->color?->name, $item['variant']->size?->name])->filter()->implode(' / ')
                        : null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'url' => route('products.show', $item['product']),
                ];
            }, $items),
            'count' => array_sum(array_column($items, 'quantity')),
            'subtotal' => array_sum(array_column($items, 'subtotal')),
        ];
    }

    /**
     * Couleurs, tailles et grille des variantes d'un produit, pour permettre au client de
     * choisir directement dans le panier (docs/SPEC.md — pas de sélection forcée à l'ajout).
     */
    private function variantOptions(Product $product): array
    {
        $variants = $product->variants()->with(['color', 'size'])->get();

        return [
            'colors' => $variants->pluck('color')->filter()->unique('id')->values()
                ->map(fn ($color) => ['id' => $color->id, 'name' => $color->name, 'hex' => $color->hex_code])
                ->all(),
            'sizes' => $variants->pluck('size')->filter()->unique('id')->values()
                ->map(fn ($size) => ['id' => $size->id, 'name' => $size->name])
                ->all(),
            'variants' => $variants
                ->map(fn ($variant) => [
                    'id' => $variant->id,
                    'color_id' => $variant->color_id,
                    'size_id' => $variant->size_id,
                    'stock' => $variant->stock,
                ])
                ->all(),
        ];
    }

    private function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    private function itemKey(int $productId, ?int $variantId): string
    {
        return $productId.':'.($variantId ?? '0');
    }

    private function parseKey(string $key): array
    {
        [$productId, $variantId] = explode(':', $key);

        return [(int) $productId, $variantId === '0' ? null : (int) $variantId];
    }
}
