<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get authenticated professional
        $professional = Auth::guard('professional')->user();

        if (!$professional) {
            return redirect()->route('professional.login');
        }

        // Get subscription
        $subscription = $professional->subscription;

        if (!$subscription) {
            // No subscription found, redirect to subscription page
            return redirect()->route('professional.subscription')
                ->with('error', 'Você precisa configurar sua assinatura para continuar.');
        }

        // Check if trial is valid (trial_ends_at is in the professional model)
        $isTrialActive = $professional->trial_ends_at && $professional->trial_ends_at->isFuture();

        // Allow access if:
        // 1. Subscription is active OR
        // 2. Subscription is on trial AND trial period hasn't expired
        if ($subscription->status === 'active' || 
            ($subscription->status === 'trial' && $isTrialActive)) {
            return $next($request);
        }

        // If subscription is cancelled or suspended and trial expired
        if (in_array($subscription->status, ['cancelled', 'suspended']) && !$isTrialActive) {
            // Allow access only to subscription page
            if ($request->routeIs('professional.subscription*')) {
                return $next($request);
            }

            return redirect()->route('professional.subscription')
                ->with('error', 'Sua assinatura expirou. Por favor, renove para continuar usando o Zapby.');
        }

        // If trial expired but subscription was never activated
        if ($subscription->status === 'trial' && !$isTrialActive) {
            // Allow access only to subscription page
            if ($request->routeIs('professional.subscription*')) {
                return $next($request);
            }

            return redirect()->route('professional.subscription')
                ->with('error', 'Seu período de teste expirou. Ative sua assinatura para continuar.');
        }

        return $next($request);
    }
}
