<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'staff' => \App\Http\Middleware\EnsureUserIsStaff::class,
        ]);

        // Volet prévention (IPS) de la surveillance applicative — avant toute route, sur
        // l'ensemble du site.
        $middleware->web(append: [\App\Http\Middleware\CheckBlockedIp::class]);

        // Pas de page de connexion dédiée (fenêtre flottante uniquement) — un invité qui tente
        // d'accéder à une page protégée est renvoyé à l'accueil avec la modale de connexion ouverte.
        $middleware->redirectGuestsTo(fn () => route('home', ['login' => 1]));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
