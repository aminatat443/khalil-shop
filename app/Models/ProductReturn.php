<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturn extends Model
{
    // Le nom "Return" étant réservé en PHP, le modèle s'appelle ProductReturn
    // mais pointe vers la table `returns` (docs/SPEC.md §2.1).
    protected $table = 'returns';

    protected $fillable = [
        'order_item_id',
        'reason',
        'description',
        'status',
        'refund_method',
        'admin_note',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
