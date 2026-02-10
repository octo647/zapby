<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            // Se estiver tentando acessar área profissional, redireciona para login profissional
            if ($request->is('professional/*')) {
                return route('professional.login');
            }
            
            return route('home');
        }
        
        return null;
    }
}
