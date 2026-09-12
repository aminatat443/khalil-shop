<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
        'blocked_by',
    ];

    protected function casts(): array
    {
        return [
            'blocked_until' => 'datetime',
        ];
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function isActive(): bool
    {
        return $this->blocked_until === null || $this->blocked_until->isFuture();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('blocked_until')->orWhere('blocked_until', '>', now()));
    }
}
