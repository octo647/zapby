<?php

namespace App\Console\Commands;

use App\Services\AsaasService;
use Illuminate\Console\Command;

class TestAsaasConnection extends Command
{
    protected $signature = 'asaas:test';
    protected $description = 'Testar conexão com API do Asaas';

    public function handle(AsaasService $asaasService)
    {
        $this->info('🔍 Testando conexão com Asaas...');
        $this->newLine();

        // Testar configurações
        $apiUrl = config('services.asaas.api_url');
        $apiKey = config('services.asaas.api_key');

        $this->info("📍 URL: {$apiUrl}");
        $this->info("🔑 API Key length: " . strlen($apiKey));
        $this->info("🔑 API Key prefix: " . substr($apiKey, 0, 20) . '...');
        $this->newLine();

        // Tentar criar um cliente de teste
        $this->info('👤 Tentando criar cliente de teste...');
        
        $testData = [
            'name' => 'Teste Zapby',
            'cpf' => '12345678900',
            'whatsapp' => '32987654321',
            'external_reference' => 'test_' . time(),
        ];

        $result = $asaasService->createCustomer($testData);

        if ($result) {
            $this->info('✅ Cliente criado com sucesso!');
            $this->info('Customer ID: ' . ($result['id'] ?? 'N/A'));
            $this->newLine();
            $this->warn('⚠️  Lembre-se de excluir este cliente de teste no painel do Asaas!');
            return 0;
        } else {
            $this->error('❌ Falha ao criar cliente!');
            $this->error('Verifique os logs em storage/logs/laravel.log');
            return 1;
        }
    }
}
