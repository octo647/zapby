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
        try {
            // Log the incoming webhook for debugging
            Log::info('Zapby Webhook Received', [
                'event' => $request->input('event'),
                'payment_id' => $request->input('payment.id'),
                'subscription_id' => $request->input('payment.subscription'),
            ]);

            // Validate webhook token - Asaas sends it in the 'asaas-access-token' header
            $webhookToken = $request->header('asaas-access-token');
            $expectedWebhookToken = config('services.asaas.webhook_token');
            
            // Validate that the webhook has the correct token for Zapby
            if ($expectedWebhookToken && $webhookToken !== $expectedWebhookToken) {
                Log::warning('Zapby Webhook: Invalid or missing token (possibly from another system)', [
                    'received_token' => $webhookToken ? substr($webhookToken, 0, 15).'...' : 'null',
                    'expected_prefix' => $expectedWebhookToken ? substr($expectedWebhookToken, 0, 15).'...' : 'not configured',
                ]);
                // Return success to avoid retry, but don't process
                return response()->json(['success' => true, 'message' => 'Token mismatch - ignored'], 200);
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
            
        } catch (\Exception $e) {
            // Log error but return 200 to avoid Asaas retrying
            Log::error('Asaas Webhook: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event' => $request->input('event'),
                'payment_id' => $request->input('payment.id'),
            ]);
            
            // Return 200 to prevent Asaas from penalizing us
            return response()->json([
                'success' => false,
                'message' => 'Error processed but acknowledged'
            ], 200);
        }
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
            // Subscription not from Zapby (probably from Pagby), ignore silently
            Log::debug('Zapby Webhook: Subscription not found in Zapby DB (event from another system)', [
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
            Log::debug('Zapby Webhook: Subscription not found (event from another system)', [
                'asaas_subscription_id' => $subscriptionId,
            ]);
            return;
        }

        // Only suspend if the payment due date has actually passed
        $dueDate = $request->input('payment.dueDate');
        if ($dueDate && \Carbon\Carbon::parse($dueDate)->isFuture()) {
            Log::info('Asaas Webhook: Payment overdue but due date is still in the future, not suspending', [
                'subscription_id' => $subscription->id,
                'due_date' => $dueDate,
            ]);
            return;
        }

        // Mark subscription as suspended (overdue payment)
        $subscription->status = 'suspended';
        $subscription->save();

        Log::info('Asaas Webhook: Subscription suspended (payment overdue)', [
            'subscription_id' => $subscription->id,
            'professional_id' => $subscription->professional_id,
            'due_date' => $dueDate,
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
            Log::debug('Zapby Webhook: Subscription not found (event from another system)', [
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
