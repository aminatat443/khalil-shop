<?php

namespace App\Http\Middleware;

use App\Services\SecurityMonitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Volet "prévention" (IPS) de la surveillance applicative — court-circuite toute requête dont
 * l'IP a été bloquée (manuellement ou automatiquement après force brute), avant qu'elle
 * n'atteigne la moindre route.
 */
class CheckBlockedIp
{
    public function __construct(private readonly SecurityMonitor $security)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->security->isBlocked($request->ip())) {
            abort(403, "Accès temporairement bloqué suite à une activité suspecte. Réessayez plus tard.");
        }

        return $next($request);
    }
}
