<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class PromotionService
{
    /**
     * Prix unitaire effectif d'un produit/variante, en tenant compte d'une promotion planifiée
     * active (table `promotions`, section 46 du cahier des charges). Coexiste avec les champs
     * products.old_price/is_promo qui servent à l'affichage manuel ponctuel (docs/SPEC.md §5).
     */
    public function effectivePrice(Product $product, ?ProductVariant $variant = null): int
    {
        $basePrice = $variant?->price ?? $product->price;

        $promotion = $this->activePromotionFor($product);

        if (! $promotion) {
            return $basePrice;
        }

        return $basePrice - $this->discountAmount($basePrice, $promotion->type, $promotion->value);
    }

    public function activePromotionFor(Product $product): ?Promotion
    {
        $now = Carbon::now();

        return Promotion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($product) {
                $query->where('product_id', $product->id)
                    ->orWhere('category_id', $product->category_id);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            // priorité à une promotion ciblant le produit précis plutôt que sa catégorie
            ->orderByRaw('product_id IS NULL')
            ->first();
    }

    /**
     * Calcule la réduction d'un code promo pour un sous-total donné (section 47 du cahier des charges).
     *
     * @throws InvalidArgumentException si le coupon n'est pas utilisable
     */
    public function couponDiscount(Coupon $coupon, int $subtotal): int
    {
        if (! $coupon->is_active) {
            throw new InvalidArgumentException("Ce code promo n'est plus actif.");
        }

        $now = Carbon::now();

        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            throw new InvalidArgumentException("Ce code promo n'est pas encore valide.");
        }

        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            throw new InvalidArgumentException('Ce code promo a expiré.');
        }

        if ($coupon->min_amount && $subtotal < $coupon->min_amount) {
            throw new InvalidArgumentException("Montant minimum de {$coupon->min_amount} FCFA requis pour ce code.");
        }

        if ($coupon->usage_limit !== null && $coupon->usages()->count() >= $coupon->usage_limit) {
            throw new InvalidArgumentException("Ce code promo a atteint sa limite d'utilisation.");
        }

        return $this->discountAmount($subtotal, $coupon->type, $coupon->value);
    }

    private function discountAmount(int $amount, string $type, int $value): int
    {
        $discount = $type === 'percentage'
            ? (int) round($amount * $value / 100)
            : $value;

        return min($discount, $amount);
    }
}
