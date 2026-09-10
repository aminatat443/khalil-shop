<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_LABELS = [
        'recue' => 'Reçue', 'confirmee' => 'Confirmée', 'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée', 'livree' => 'Livrée', 'annulee' => 'Annulée',
    ];

    public const STATUS_TONES = [
        'recue' => 'neutral', 'confirmee' => 'blue', 'en_preparation' => 'amber',
        'expediee' => 'primary', 'livree' => 'green', 'annulee' => 'red',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'coupon_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_region',
        'delivery_city',
        'delivery_quartier',
        'delivery_address',
        'delivery_instructions',
        'subtotal',
        'delivery_fee',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Statut à afficher au client quand un retour a été demandé sur au moins un article de la
     * commande : prime sur le statut logistique normal (ex. "Expédiée") pour éviter de montrer
     * un statut obsolète pendant qu'un retour est en cours. Nécessite `items.returns` chargé.
     */
    public function returnStatusInfo(): ?array
    {
        $statuses = $this->items->flatMap(fn (OrderItem $item) => $item->returns->pluck('status'));

        if ($statuses->isEmpty()) {
            return null;
        }

        $labels = [
            'demandee' => 'Demande de retour en cours',
            'acceptee' => 'Retour accepté',
            'article_recu' => 'Article reçu (retour)',
            'refusee' => 'Retour refusé',
            'remboursee' => 'Retournée',
        ];

        // Priorité à l'état le plus "actif" quand les articles d'une même commande ont des
        // statuts de retour différents.
        foreach (['demandee', 'acceptee', 'article_recu', 'refusee', 'remboursee'] as $status) {
            if ($statuses->contains($status)) {
                return ['label' => $labels[$status], 'tone' => ProductReturn::STATUS_TONES[$status]];
            }
        }

        return null;
    }

    public function hasAnyReturn(): bool
    {
        return $this->items->contains(fn (OrderItem $item) => $item->returns->isNotEmpty());
    }
}
