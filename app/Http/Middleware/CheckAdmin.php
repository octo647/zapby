<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('professional')->check()) {
            return redirect()->route('professional.login')
                ->with('error', 'Você precisa estar autenticado para acessar esta área.');
        }

        if (!auth('professional')->user()->is_admin) {
            abort(403, 'Acesso negado. Área restrita para administradores.');
        }

        return $next($request);
    }
}
