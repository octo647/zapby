<?php

namespace App\Console\Commands;

use App\Models\Professional;
use App\Services\AsaasService;
use Illuminate\Console\Command;

class TestSubscriptionFlow extends Command
{
    protected $signature = 'test:subscription {professional_id}';
    protected $description = 'Testar fluxo completo de ativação de assinatura';

    public function handle(AsaasService $asaasService): int
    {
        $professionalId = $this->argument('professional_id');
        
        $this->info("🔄 Testando ativação de assinatura...\n");
        
        // 1. Buscar profissional
        $professional = Professional::with('subscription')->find($professionalId);
        
        if (!$professional) {
            $this->error("❌ Profissional não encontrado!");
            return self::FAILURE;
        }
        
        $this->info("👤 Profissional: {$professional->name}");
        $this->info("📱 WhatsApp: {$professional->whatsapp}");
        $this->info("📝 CPF: " . ($professional->cpf ?? 'Não informado'));
        $this->newLine();
        
        $subscription = $professional->subscription;
        
        if (!$subscription) {
            $this->error("❌ Assinatura não encontrada!");
            return self::FAILURE;
        }
        
        $this->info("📊 Status atual: {$subscription->status}");
        $this->newLine();
        
        // 2. Criar cliente no Asaas
        if (!$subscription->asaas_customer_id) {
            $this->info("📝 Criando cliente no Asaas...");
            
            $customerData = $asaasService->createCustomer([
                'name' => $professional->name,
                'whatsapp' => $professional->whatsapp,
                'cpf' => $professional->cpf,
                'external_reference' => "professional_{$professional->id}",
            ]);
            
            if (!$customerData) {
                $this->error("❌ Falha ao criar cliente no Asaas!");
                $this->error("Verifique os logs em storage/logs/laravel.log");
                return self::FAILURE;
            }
            
            $this->info("✅ Cliente criado: {$customerData['id']}");
            
            $subscription->asaas_customer_id = $customerData['id'];
            $subscription->save();
        } else {
            $this->info("ℹ️  Cliente já existe: {$subscription->asaas_customer_id}");
        }
        
        $this->newLine();
        
        // 3. Criar assinatura no Asaas
        if (!$subscription->asaas_subscription_id) {
            $this->info("💳 Criando assinatura no Asaas...");
            
            $subscriptionData = $asaasService->createSubscription(
                $subscription->asaas_customer_id,
                [
                    'value' => 19.90,
                    'next_due_date' => now()->addDays(7)->format('Y-m-d'),
                    'cycle' => 'MONTHLY',
                    'billing_type' => 'PIX',
                    'description' => 'Assinatura Mensal Zapby - Teste',
                    'external_reference' => "subscription_{$subscription->id}",
                ]
            );
            
            if (!$subscriptionData) {
                $this->error("❌ Falha ao criar assinatura no Asaas!");
                $this->error("Verifique os logs em storage/logs/laravel.log");
                return self::FAILURE;
            }
            
            $this->info("✅ Assinatura criada: {$subscriptionData['id']}");
            $this->info("📅 Próximo vencimento: {$subscriptionData['nextDueDate']}");
            $this->info("💰 Valor: R$ {$subscriptionData['value']}");
            $this->info("🔄 Ciclo: {$subscriptionData['cycle']}");
            
            $subscription->asaas_subscription_id = $subscriptionData['id'];
            $subscription->status = 'active';
            $subscription->save();
        } else {
            $this->info("ℹ️  Assinatura já existe: {$subscription->asaas_subscription_id}");
        }
        
        $this->newLine();
        $this->info("✅ Teste concluído com sucesso!");
        $this->info("📊 Status final: {$subscription->status}");
        $this->info("🎫 Customer ID: {$subscription->asaas_customer_id}");
        $this->info("💳 Subscription ID: {$subscription->asaas_subscription_id}");
        
        return self::SUCCESS;
    }
}
