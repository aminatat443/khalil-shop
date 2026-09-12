<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    public const TYPES = [
        'failed_login' => 'Connexion échouée',
        'brute_force' => 'Force brute détectée',
        'unauthorized_access' => 'Accès non autorisé',
        'ip_blocked' => 'IP bloquée',
        'ip_unblocked' => 'IP débloquée',
    ];

    public const SEVERITY_TONES = [
        'info' => 'blue',
        'warning' => 'amber',
        'critical' => 'red',
    ];

    protected $fillable = [
        'type',
        'severity',
        'ip_address',
        'user_id',
        'email',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
