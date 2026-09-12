<?php

namespace App\Services;

use App\Models\BlockedIp;
use App\Models\SecurityEvent;
use App\Models\User;
use App\Notifications\SecurityAlertNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

/**
 * Surveillance et prévention applicative (pas un IDS/IPS réseau type Snort/Suricata) : détecte
 * les schémas d'abus au niveau de l'application Laravel (force brute, accès non autorisés) et
 * peut bloquer une IP en conséquence. Voir la section "Sécurité" du back-office.
 */
class SecurityMonitor
{
    /**
     * Nombre de connexions échouées, dans la fenêtre ci-dessous, déclenchant un blocage auto.
     */
    private const BRUTE_FORCE_THRESHOLD = 5;

    private const BRUTE_FORCE_WINDOW_MINUTES = 15;

    private const AUTO_BLOCK_MINUTES = 30;

    public function log(string $type, Request $request, string $message, string $severity = 'info', ?string $email = null): SecurityEvent
    {
        return SecurityEvent::create([
            'type' => $type,
            'severity' => $severity,
            'ip_address' => $request->ip(),
            'user_id' => $request->user()?->id,
            'email' => $email,
            'message' => $message,
        ]);
    }

    public function recordFailedLogin(Request $request, string $email): void
    {
        $this->log('failed_login', $request, "Échec de connexion pour {$email}", 'warning', $email);

        $recentFailures = SecurityEvent::where('ip_address', $request->ip())
            ->where('type', 'failed_login')
            ->where('created_at', '>=', now()->subMinutes(self::BRUTE_FORCE_WINDOW_MINUTES))
            ->count();

        if ($recentFailures >= self::BRUTE_FORCE_THRESHOLD && ! $this->isBlocked($request->ip())) {
            $this->block(
                $request->ip(),
                "{$recentFailures} échecs de connexion en moins de ".self::BRUTE_FORCE_WINDOW_MINUTES.' minutes',
                self::AUTO_BLOCK_MINUTES,
            );

            $this->log('brute_force', $request, "Force brute détectée — IP bloquée automatiquement {$request->ip()}", 'critical', $email);

            Notification::send(User::staff()->get(), new SecurityAlertNotification(
                "Force brute détectée depuis {$request->ip()} — bloquée automatiquement ".self::AUTO_BLOCK_MINUTES.' min.'
            ));
        }
    }

    public function recordUnauthorizedAccess(Request $request, string $message): void
    {
        $this->log('unauthorized_access', $request, $message, 'warning');
    }

    public function isBlocked(string $ip): bool
    {
        return BlockedIp::active()->where('ip_address', $ip)->exists();
    }

    public function block(string $ip, string $reason, ?int $minutes = null, ?int $byUserId = null): BlockedIp
    {
        return BlockedIp::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_until' => $minutes ? now()->addMinutes($minutes) : null,
                'blocked_by' => $byUserId,
            ]
        );
    }

    public function unblock(string $ip): void
    {
        BlockedIp::where('ip_address', $ip)->delete();
    }
}
