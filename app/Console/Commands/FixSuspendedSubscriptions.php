<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\AsaasService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixSuspendedSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:fix-suspended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix suspended subscriptions that should be active based on next_due_date';

    /**
     * Execute the console command.
     */
    public function handle(AsaasService $asaasService)
    {
        $this->info('Checking suspended subscriptions...');

        // Find suspended subscriptions with future next_due_date
        $suspendedWithFuturePayment = Subscription::where('status', 'suspended')
            ->where('next_due_date', '>', Carbon::now())
            ->get();

        if ($suspendedWithFuturePayment->isEmpty()) {
            $this->info('No incorrectly suspended subscriptions found.');
            return 0;
        }

        $this->info("Found {$suspendedWithFuturePayment->count()} suspended subscriptions with future payment dates.");

        $fixed = 0;
        $failed = 0;

        foreach ($suspendedWithFuturePayment as $subscription) {
            $this->line("\nSubscription #{$subscription->id}:");
            $this->line("  Professional: {$subscription->professional->name}");
            $this->line("  Current Status: {$subscription->status}");
            $this->line("  Next Due Date: {$subscription->next_due_date->format('d/m/Y')}");

            // If has Asaas subscription, verify status and payments
            if ($subscription->asaas_subscription_id) {
                try {
                    $asaasSubscription = $asaasService->getSubscription($subscription->asaas_subscription_id);

                    if ($asaasSubscription) {
                        $asaasStatus = $asaasSubscription['status'] ?? 'UNKNOWN';
                        $this->line("  Asaas Status: {$asaasStatus}");

                        // Check payments to see if there's a confirmed/received payment
                        $paymentsData = $asaasService->getSubscriptionPayments($subscription->asaas_subscription_id);
                        $payments = $paymentsData['data'] ?? [];
                        
                        $hasValidPayment = false;
                        foreach ($payments as $payment) {
                            if (in_array($payment['status'], ['RECEIVED', 'CONFIRMED'])) {
                                $dueDate = \Carbon\Carbon::parse($payment['dueDate']);
                                $this->line("  Last Payment: {$payment['status']} on {$dueDate->format('d/m/Y')}");
                                $hasValidPayment = true;
                                break;
                            }
                        }

                        // If subscription has future next_due_date and has received payment,
                        // it should be active regardless of Asaas subscription status
                        if ($hasValidPayment && $subscription->next_due_date->isFuture()) {
                            $subscription->status = 'active';
                            $subscription->save();
                            
                            $this->info("  ✅ FIXED: Changed to active (has valid payment until {$subscription->next_due_date->format('d/m/Y')})");
                            $fixed++;
                        } elseif ($asaasStatus === 'ACTIVE') {
                            $subscription->status = 'active';
                            $subscription->save();
                            
                            $this->info("  ✅ FIXED: Changed to active (Asaas status is ACTIVE)");
                            $fixed++;
                        } else {
                            $this->warn("  ⚠️  No valid payment found and Asaas status is {$asaasStatus}, keeping suspended");
                            $failed++;
                        }
                    } else {
                        $this->warn("  ⚠️  Could not fetch Asaas subscription");
                        $failed++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Error: {$e->getMessage()}");
                    $failed++;
                }
            } else {
                // No Asaas subscription ID, but has future payment date
                // This might be a data inconsistency, mark as active
                $subscription->status = 'active';
                $subscription->save();
                
                $this->info("  ✅ FIXED: Changed to active (no Asaas ID)");
                $fixed++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Fixed: {$fixed}");
        if ($failed > 0) {
            $this->warn("  Failed/Skipped: {$failed}");
        }

        return 0;
    }
}
