<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * Réserve le back-office aux rôles Gestionnaire / Administrateur / Super Administrateur
     * (docs/SPEC.md §2.5). Le Client n'a jamais accès à /admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isGestionnaire()) {
            abort(403, "Accès réservé à l'équipe KhalilShop.");
        }

        return $next($request);
    }
}
