<?php

namespace App\Console\Commands;

use App\Models\Professional;
use App\Models\Subscription;
use App\Services\AsaasService;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'subscription:check {professional_id?}';
    protected $description = 'Check subscription status for a professional';

    public function handle(AsaasService $asaasService)
    {
        $professionalId = $this->argument('professional_id');

        if ($professionalId) {
            $this->checkProfessional($professionalId, $asaasService);
        } else {
            $this->listAllSubscriptions();
        }

        return 0;
    }

    private function checkProfessional($id, AsaasService $asaasService)
    {
        $professional = Professional::with('subscription')->find($id);

        if (!$professional) {
            $this->error("Professional ID {$id} not found!");
            return;
        }

        $this->info("=== Professional #{$id} ===");
        $this->line("Name: {$professional->name}");
        $this->line("Email: {$professional->email}");
        $this->line("WhatsApp: {$professional->whatsapp}");
        $this->line("Trial ends: " . ($professional->trial_ends_at ? $professional->trial_ends_at->format('d/m/Y H:i') : 'N/A'));
        $this->newLine();

        $subscription = $professional->subscription;

        if (!$subscription) {
            $this->warn("No subscription found!");
            return;
        }

        $this->info("=== Local Subscription ===");
        $this->line("ID: {$subscription->id}");
        $this->line("Status: {$subscription->status}");
        $this->line("Next Due Date: " . ($subscription->next_due_date ? $subscription->next_due_date->format('d/m/Y') : 'N/A'));
        $this->line("Amount: R$ " . number_format($subscription->amount, 2, ',', '.'));
        $this->line("Asaas Customer ID: {$subscription->asaas_customer_id}");
        $this->line("Asaas Subscription ID: {$subscription->asaas_subscription_id}");
        $this->newLine();

        if ($subscription->asaas_subscription_id) {
            $this->info("=== Asaas Subscription ===");
            
            try {
                $asaasData = $asaasService->getSubscription($subscription->asaas_subscription_id);
                
                if ($asaasData) {
                    $this->line("Status: " . ($asaasData['status'] ?? 'N/A'));
                    $this->line("Next Due Date: " . ($asaasData['nextDueDate'] ?? 'N/A'));
                    $this->line("Value: R$ " . ($asaasData['value'] ?? 'N/A'));
                    $this->newLine();
                } else {
                    $this->warn("Could not fetch Asaas subscription data");
                }

                // Check payments
                $this->info("=== Recent Payments ===");
                $paymentsData = $asaasService->getSubscriptionPayments($subscription->asaas_subscription_id);
                $payments = $paymentsData['data'] ?? [];

                if (empty($payments)) {
                    $this->warn("No payments found");
                } else {
                    foreach (array_slice($payments, 0, 3) as $payment) {
                        $status = $payment['status'] ?? 'N/A';
                        $dueDate = $payment['dueDate'] ?? 'N/A';
                        $value = $payment['value'] ?? 'N/A';
                        
                        $statusColor = in_array($status, ['RECEIVED', 'CONFIRMED']) ? 'info' : 'comment';
                        $this->{$statusColor}("  {$dueDate} - R$ {$value} - {$status}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
            }
        }
    }

    private function listAllSubscriptions()
    {
        $this->info("=== All Subscriptions ===");
        $this->newLine();

        $subscriptions = Subscription::with('professional')->get();

        $headers = ['ID', 'Professional', 'Status', 'Next Due Date', 'Asaas ID'];
        $rows = [];

        foreach ($subscriptions as $sub) {
            $rows[] = [
                $sub->id,
                $sub->professional->name ?? 'N/A',
                $sub->status,
                $sub->next_due_date ? $sub->next_due_date->format('d/m/Y') : 'N/A',
                $sub->asaas_subscription_id ? '...' . substr($sub->asaas_subscription_id, -8) : 'N/A',
            ];
        }

        $this->table($headers, $rows);
    }
}
