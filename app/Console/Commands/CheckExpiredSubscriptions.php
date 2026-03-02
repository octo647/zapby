<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\AsaasService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update their status';

    /**
     * Execute the console command.
     */
    public function handle(AsaasService $asaasService)
    {
        $this->info('Checking for expired subscriptions...');

        // Find active subscriptions with overdue next_due_date
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('next_due_date', '<', Carbon::now())
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions.");

        foreach ($expiredSubscriptions as $subscription) {
            try {
                // Check subscription status in Asaas
                $asaasSubscription = $asaasService->getSubscription($subscription->asaas_subscription_id);

                if ($asaasSubscription && isset($asaasSubscription['status'])) {
                    $asaasStatus = $asaasSubscription['status'];

                    // Update local status based on Asaas status
                    if ($asaasStatus === 'ACTIVE') {
                        // Subscription is still active in Asaas, update next due date
                        if (isset($asaasSubscription['nextDueDate'])) {
                            $subscription->next_due_date = Carbon::parse($asaasSubscription['nextDueDate']);
                            $subscription->save();
                            
                            $this->info("Updated subscription #{$subscription->id} - next due date: {$subscription->next_due_date}");
                        }
                    } else {
                        // Subscription is no longer active in Asaas
                        $subscription->status = 'suspended';
                        $subscription->save();

                        $this->warn("Suspended subscription #{$subscription->id} - Asaas status: {$asaasStatus}");
                    }
                } else {
                    // Could not fetch subscription from Asaas, mark as suspended
                    $subscription->status = 'suspended';
                    $subscription->save();

                    $this->warn("Suspended subscription #{$subscription->id} - Could not fetch from Asaas");
                }
            } catch (\Exception $e) {
                $this->error("Error checking subscription #{$subscription->id}: {$e->getMessage()}");
                continue;
            }
        }

        // Also check trial subscriptions that expired
        $expiredTrials = Subscription::where('status', 'trial')
            ->where('trial_ends_at', '<', Carbon::now())
            ->get();

        if ($expiredTrials->count() > 0) {
            $this->info("Found {$expiredTrials->count()} expired trial subscriptions.");

            foreach ($expiredTrials as $subscription) {
                // Check if they have an active Asaas subscription
                if ($subscription->asaas_subscription_id) {
                    try {
                        $asaasSubscription = $asaasService->getSubscription($subscription->asaas_subscription_id);

                        if ($asaasSubscription && $asaasSubscription['status'] === 'ACTIVE') {
                            // Trial ended but they activated subscription
                            $subscription->status = 'active';
                            $subscription->save();

                            $this->info("Activated subscription #{$subscription->id} (trial converted)");
                        } else {
                            // Trial ended and no active subscription
                            $subscription->status = 'suspended';
                            $subscription->save();

                            $this->warn("Suspended subscription #{$subscription->id} (trial expired)");
                        }
                    } catch (\Exception $e) {
                        $this->error("Error checking trial subscription #{$subscription->id}: {$e->getMessage()}");
                    }
                } else {
                    // Trial ended and never activated
                    $subscription->status = 'suspended';
                    $subscription->save();

                    $this->warn("Suspended subscription #{$subscription->id} (trial expired, never activated)");
                }
            }
        }

        $this->info('Subscription check completed!');
        return 0;
    }
}
