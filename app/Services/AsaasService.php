<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.asaas.api_url');
        $this->apiKey = config('services.asaas.api_key');
    }

    /**
     * Criar cliente no Asaas
     */
    public function createCustomer(array $data): ?array
    {
        try {
            $cpf = preg_replace('/[^0-9]/', '', $data['cpf'] ?? '');
            $phone = $data['phone'] ?? $data['whatsapp'] ?? '';
            
            $payload = [
                'name' => $data['name'],
                'cpfCnpj' => $cpf,
                'externalReference' => $data['external_reference'] ?? null,
            ];
            
            // Adicionar telefone se fornecido
            if ($phone) {
                $formattedPhone = $this->formatPhone($phone);
                $payload['phone'] = $formattedPhone;
                $payload['mobilePhone'] = $formattedPhone;
            }
            
            // Adicionar email se fornecido
            if (!empty($data['email'])) {
                $payload['email'] = $data['email'];
            }
            
            Log::info('Asaas: Criando cliente', [
                'url' => "{$this->apiUrl}/customers",
                'api_key_prefix' => substr($this->apiKey, 0, 10).'...',
                'payload' => array_merge($payload, ['cpfCnpj' => substr($cpf, 0, 3).'***']),
            ]);
            
            $response = Http::withHeaders([
                    'access_token' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->apiUrl}/customers", $payload);

            if ($response->successful()) {
                Log::info('Asaas: Cliente criado com sucesso', [
                    'customer_id' => $response->json()['id'] ?? null,
                ]);
                return $response->json();
            }

            $errorResponse = $response->json();
            Log::error('Asaas: Erro ao criar cliente', [
                'status' => $response->status(),
                'errors' => $errorResponse['errors'] ?? [],
                'message' => $errorResponse['message'] ?? 'Erro desconhecido',
                'response' => $errorResponse,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Exceção ao criar cliente', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Criar assinatura recorrente
     */
    public function createSubscription(string $customerId, array $data): ?array
    {
        try {
            $payload = [
                'customer' => $customerId,
                'billingType' => $data['billing_type'] ?? 'PIX',
                'value' => $data['value'] ?? 19.90,
                'nextDueDate' => $data['next_due_date'],
                'cycle' => $data['cycle'] ?? 'MONTHLY',
                'description' => $data['description'] ?? 'Assinatura Zapby',
                'externalReference' => $data['external_reference'] ?? null,
            ];
            
            Log::info('Asaas: Criando assinatura', [
                'url' => "{$this->apiUrl}/subscriptions",
                'customer_id' => $customerId,
                'value' => $payload['value'],
                'next_due_date' => $payload['nextDueDate'],
            ]);
            
            $response = Http::withHeaders([
                    'access_token' => $this->apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->apiUrl}/subscriptions", $payload);

            if ($response->successful()) {
                Log::info('Asaas: Assinatura criada com sucesso', [
                    'subscription_id' => $response->json()['id'] ?? null,
                ]);
                return $response->json();
            }

            $errorResponse = $response->json();
            Log::error('Asaas: Erro ao criar assinatura', [
                'status' => $response->status(),
                'errors' => $errorResponse['errors'] ?? [],
                'message' => $errorResponse['message'] ?? 'Erro desconhecido',
                'response' => $errorResponse,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Exceção ao criar assinatura', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Cancelar assinatura
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->delete("{$this->apiUrl}/subscriptions/{$subscriptionId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Asaas: Erro ao cancelar assinatura', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Buscar informações de uma assinatura
     */
    public function getSubscription(string $subscriptionId): ?array
    {
        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->get("{$this->apiUrl}/subscriptions/{$subscriptionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Erro ao buscar assinatura', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Buscar pagamentos de uma assinatura
     */
    public function getSubscriptionPayments(string $subscriptionId): ?array
    {
        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->get("{$this->apiUrl}/subscriptions/{$subscriptionId}/payments");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Erro ao buscar pagamentos', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Buscar informações de um pagamento
     */
    public function getPayment(string $paymentId): ?array
    {
        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->get("{$this->apiUrl}/payments/{$paymentId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Erro ao buscar pagamento', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Gerar QR Code PIX para um pagamento
     */
    public function getPixQrCode(string $paymentId): ?array
    {
        try {
            $response = Http::withHeaders(['access_token' => $this->apiKey])
                ->get("{$this->apiUrl}/payments/{$paymentId}/pixQrCode");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Asaas: Erro ao buscar QR Code PIX', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Formatar telefone para o padrão do Asaas
     */
    private function formatPhone(string $phone): string
    {
        // Remove tudo que não é número
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Garante que tem DDD
        if (strlen($phone) === 11) {
            return $phone;
        }
        
        return $phone;
    }
}
