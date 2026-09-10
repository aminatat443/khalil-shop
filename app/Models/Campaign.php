<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    public const TYPES = [
        'nouveautes' => 'Nouveautés',
        'promotion' => 'Promotion',
        'reassort' => 'Réassort de stock',
        'annonce' => 'Annonce générale',
    ];

    protected $fillable = [
        'type',
        'subject',
        'message',
        'product_ids',
        'recipients_count',
        'sent_by',
    ];

    protected function casts(): array
    {
        return [
            'product_ids' => 'array',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function products()
    {
        return Product::whereIn('id', $this->product_ids)
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
            ->get();
    }
}
