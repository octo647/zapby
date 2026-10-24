<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('professional/*')) {
                return route('professional.login');
            }
            return route('home');
        });

        // Exclude webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
            'webhook/*',
        ]);

        // Register middleware aliases
        $middleware->alias([
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Token expirado no logout: encerra a sessão mesmo assim em vez de exibir o 419.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || ! $request->routeIs('professional.logout')) {
                return null;
            }

            Auth::guard('professional')->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('professional.login');
        });
    })->create();
