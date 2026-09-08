<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PromotionService $promotions,
    ) {
    }

    /**
     * Crée la commande à partir du panier courant (checkout en 5 étapes, section 34 du cahier des charges).
     * Ne décrémente pas le stock ici — cela se fait uniquement à la confirmation (docs/SPEC.md §2.3/§2.4),
     * un article au panier n'est jamais réservé.
     */
    public function createFromCart(array $customer, array $delivery, string $paymentMethod, int $deliveryFee, ?Coupon $coupon = null): Order
    {
        $items = $this->cart->items();

        if (empty($items)) {
            throw new RuntimeException('Le panier est vide.');
        }

        $subtotal = array_sum(array_column($items, 'subtotal'));
        $discount = $coupon ? $this->promotions->couponDiscount($coupon, $subtotal) : 0;

        return DB::transaction(function () use ($items, $customer, $delivery, $paymentMethod, $deliveryFee, $coupon, $subtotal, $discount) {
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $customer['user_id'] ?? null,
                'address_id' => $delivery['address_id'] ?? null,
                'coupon_id' => $coupon?->id,
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'delivery_region' => $delivery['region'],
                'delivery_city' => $delivery['city'],
                'delivery_quartier' => $delivery['quartier'] ?? null,
                'delivery_address' => $delivery['address'],
                'delivery_instructions' => $delivery['instructions'] ?? null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => max(0, $subtotal + $deliveryFee - $discount),
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'status' => 'recue',
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name,
                    'variant_label' => $this->variantLabel($item['variant']),
                    'sku' => $item['variant']?->sku,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($coupon) {
                $coupon->usages()->create([
                    'order_id' => $order->id,
                    'user_id' => $customer['user_id'] ?? null,
                ]);
            }

            $this->cart->clear();

            return $order;
        });
    }

    /**
     * Confirme la commande : décrément atomique du stock (docs/SPEC.md §2.3). Appelé automatiquement
     * après un paiement en ligne réussi, ou manuellement par un Gestionnaire/Administrateur pour le
     * paiement à la livraison (§2.4). Si une rupture de stock est détectée à cet instant précis (cas
     * de concurrence rare), la commande est annulée et rien n'est décrémenté.
     */
    public function confirm(Order $order): bool
    {
        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $affected = $item->product_variant_id
                        ? ProductVariant::where('id', $item->product_variant_id)
                            ->where('stock', '>=', $item->quantity)
                            ->decrement('stock', $item->quantity)
                        : Product::where('id', $item->product_id)
                            ->where('stock', '>=', $item->quantity)
                            ->decrement('stock', $item->quantity);

                    if (! $affected) {
                        throw new RuntimeException("Stock insuffisant pour {$item->product_name}.");
                    }
                }

                $order->update(['status' => 'confirmee']);
            });

            return true;
        } catch (RuntimeException $e) {
            $order->update([
                'status' => 'annulee',
                'admin_notes' => 'Rupture de stock détectée à la confirmation : '.$e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Annule la commande. Si le stock avait déjà été décrémenté (commande confirmée ou en préparation),
     * il est automatiquement remis en stock (docs/SPEC.md §2.3).
     */
    public function cancel(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if (in_array($order->status, ['confirmee', 'en_preparation'], true)) {
                foreach ($order->items as $item) {
                    if ($item->product_variant_id) {
                        ProductVariant::where('id', $item->product_variant_id)->increment('stock', $item->quantity);
                    } else {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update(['status' => 'annulee']);
        });
    }

    /**
     * Rattache au compte les commandes passées en tant qu'invité (sans compte), en les
     * retrouvant par email ou numéro de téléphone — dès la connexion, l'inscription, ou
     * l'ajout d'un numéro de téléphone (adresse par défaut, commande) sur un compte existant.
     */
    public function syncGuestOrders(User $user, ?string $phone = null): void
    {
        Order::whereNull('user_id')
            ->where(function ($query) use ($user, $phone) {
                $query->where('customer_email', $user->email);

                if ($phone) {
                    $query->orWhere('customer_phone', $phone);
                }
            })
            ->update(['user_id' => $user->id]);
    }

    private function generateOrderNumber(): string
    {
        return 'KH-'.str_pad((string) (Order::max('id') + 1), 6, '0', STR_PAD_LEFT);
    }

    private function variantLabel(?ProductVariant $variant): ?string
    {
        if (! $variant) {
            return null;
        }

        return collect([$variant->color?->name, $variant->size?->name])->filter()->implode(' / ');
    }
}
