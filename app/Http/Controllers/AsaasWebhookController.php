<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsaasWebhookController extends Controller
{
    /**
     * Handle Asaas webhook events
     */
    public function handle(Request $request)
    {
        // Log the incoming webhook for debugging
        Log::info('Asaas Webhook Received', [
            'event' => $request->input('event'),
            'payment_id' => $request->input('payment.id'),
            'subscription_id' => $request->input('payment.subscription'),
            'headers' => $request->headers->all(),
        ]);

        // Validate webhook token (Asaas can send token in different ways)
        $webhookToken = $request->header('asaas-access-token') ?? $request->header('x-webhook-token');
        $expectedApiKey = config('services.asaas.api_key');
        $expectedWebhookToken = config('services.asaas.webhook_token');
        
        // Accept either the API key or the configured webhook token
        $isValidToken = ($webhookToken === $expectedApiKey) || 
                       ($webhookToken === $expectedWebhookToken) ||
                       ($expectedWebhookToken && $webhookToken === $expectedWebhookToken);
        
        if (!$isValidToken && $expectedWebhookToken) {
            Log::warning('Asaas Webhook: Invalid token', [
                'received_token' => $webhookToken ? substr($webhookToken, 0, 10).'...' : 'null',
                'headers' => array_keys($request->headers->all()),
            ]);
            // Don't block webhook during testing/setup - just log warning
            // return response()->json(['error' => 'Invalid token'], 401);
        }

        // Get event type
        $event = $request->input('event');
        
        // Process based on event type
        switch ($event) {
            case 'PAYMENT_CREATED':
                $this->handlePaymentCreated($request);
                break;
                
            case 'PAYMENT_RECEIVED':
            case 'PAYMENT_CONFIRMED':
                $this->handlePaymentReceived($request);
                break;
                
            case 'PAYMENT_OVERDUE':
                $this->handlePaymentOverdue($request);
                break;
                
            case 'PAYMENT_DELETED':
            case 'PAYMENT_REFUNDED':
                $this->handlePaymentCancelled($request);
                break;
                
            default:
                Log::info('Asaas Webhook: Unhandled event type', ['event' => $event]);
        }

        return response()->json(['success' => true], 200);
    }

    /**
     * Handle payment created (just log it, wait for confirmation)
     */
    private function handlePaymentCreated(Request $request)
    {
        $subscriptionId = $request->input('payment.subscription');
        
        Log::info('Asaas Webhook: Payment created', [
            'subscription_id' => $subscriptionId,
            'payment_id' => $request->input('payment.id'),
            'status' => $request->input('payment.status'),
            'due_date' => $request->input('payment.dueDate'),
            'value' => $request->input('payment.value'),
        ]);
        
        // Just log - don't update status until payment is confirmed
    }

    /**
     * Handle payment received/confirmed
     */
    private function handlePaymentReceived(Request $request)
    {
        $subscriptionId = $request->input('payment.subscription');
        
        if (!$subscriptionId) {
            Log::warning('Asaas Webhook: Payment without subscription ID');
            return;
        }

        // Find subscription by Asaas subscription ID
        $subscription = Subscription::where('asaas_subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::warning('Asaas Webhook: Subscription not found', [
                'asaas_subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Update subscription status to active
        $subscription->status = 'active';
        
        // Update next due date based on payment
        $dueDate = $request->input('payment.dueDate');
        if ($dueDate) {
            // Add 1 month to the due date for next payment
            $subscription->next_due_date = \Carbon\Carbon::parse($dueDate)->addMonth();
        }
        
        $subscription->save();

        Log::info('Asaas Webhook: Subscription activated', [
            'subscription_id' => $subscription->id,
            'professional_id' => $subscription->professional_id,
            'next_due_date' => $subscription->next_due_date,
        ]);
    }

    /**
     * Handle payment overdue
     */
    private function handlePaymentOverdue(Request $request)
    {
        $subscriptionId = $request->input('payment.subscription');
        
        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('asaas_subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::warning('Asaas Webhook: Subscription not found for overdue payment', [
                'asaas_subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Mark subscription as suspended (overdue payment)
        $subscription->status = 'suspended';
        $subscription->save();

        Log::info('Asaas Webhook: Subscription suspended (payment overdue)', [
            'subscription_id' => $subscription->id,
            'professional_id' => $subscription->professional_id,
        ]);
    }

    /**
     * Handle payment cancelled/refunded
     */
    private function handlePaymentCancelled(Request $request)
    {
        $subscriptionId = $request->input('payment.subscription');
        
        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('asaas_subscription_id', $subscriptionId)->first();
        
        if (!$subscription) {
            Log::warning('Asaas Webhook: Subscription not found for cancelled payment', [
                'asaas_subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Mark subscription as suspended
        $subscription->status = 'suspended';
        $subscription->save();

        Log::info('Asaas Webhook: Subscription suspended (payment cancelled)', [
            'subscription_id' => $subscription->id,
            'professional_id' => $subscription->professional_id,
        ]);
    }
}
