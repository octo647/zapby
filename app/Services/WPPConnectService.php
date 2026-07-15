<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WPPConnectService
{
    private string $baseUrl;
    private string $secretKey;
    private string $session;

    public function __construct()
    {
        $this->baseUrl = config('services.wppconnect.url', 'http://69.6.222.77:21465');
        $this->secretKey = config('services.wppconnect.secret', 'Zapby2026Secret');
        $this->session = config('services.wppconnect.session', 'zapby');
    }

    /**
     * Inicia sessão e retorna informações
     */
    public function startSession(): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->secretKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/{$this->session}/start-session");

            if ($response->successful()) {
                Log::info('WPPConnect: Sessão iniciada com sucesso');
                return $response->json();
            }

            Log::error('WPPConnect: Erro ao iniciar sessão', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Falha ao iniciar sessão',
            ];
        } catch (\Exception $e) {
            Log::error('WPPConnect: Exceção ao iniciar sessão', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtém QR Code em Base64
     */
    public function getQRCode(): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->secretKey}",
                ])
                ->get("{$this->baseUrl}/api/{$this->session}/qrcode-session");

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['qrcode']) && !empty($data['qrcode'])) {
                    return $data['qrcode'];
                }
                
                if (isset($data['urlCode']) && !empty($data['urlCode'])) {
                    // Alguns retornam urlCode ao invés de qrcode
                    return $data['urlCode'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('WPPConnect: Erro ao obter QR Code', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Verifica status da conexão
     */
    public function checkConnection(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->secretKey}",
                ])
                ->get("{$this->baseUrl}/api/{$this->session}/check-connection-session");

            if ($response->successful()) {
                $data = $response->json();
                
                // Cachear status por 30 segundos
                if (isset($data['state']) && $data['state'] === 'CONNECTED') {
                    Cache::put("whatsapp_connected_{$this->session}", true, 30);
                }
                
                return $data;
            }

            return [
                'connected' => false,
                'state' => 'DISCONNECTED',
            ];
        } catch (\Exception $e) {
            Log::error('WPPConnect: Erro ao verificar conexão', [
                'message' => $e->getMessage(),
            ]);

            return [
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verifica se está conectado (usa cache)
     */
    public function isConnected(): bool
    {
        // Verifica cache primeiro
        if (Cache::has("whatsapp_connected_{$this->session}")) {
            return true;
        }

        // Verifica no servidor
        $status = $this->checkConnection();
        
        return isset($status['state']) && $status['state'] === 'CONNECTED';
    }

    /**
     * Envia mensagem de texto
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            // Formatar número (remover caracteres especiais)
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Adicionar código do país se não tiver
            if (!str_starts_with($phone, '55')) {
                $phone = '55' . $phone;
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->secretKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/{$this->session}/send-message", [
                    'phone' => $phone,
                    'message' => $message,
                    'isGroup' => false,
                ]);

            if ($response->successful()) {
                Log::info('WPPConnect: Mensagem enviada', [
                    'phone' => $phone,
                ]);

                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('WPPConnect: Erro ao enviar mensagem', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Falha ao enviar mensagem',
            ];
        } catch (\Exception $e) {
            Log::error('WPPConnect: Exceção ao enviar mensagem', [
                'phone' => $phone,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Envia lembrete de agendamento
     */
    public function sendAppointmentReminder(
        string $phone,
        string $professionalName,
        string $dateTime,
        ?string $serviceName = null
    ): bool {
        $message = "🔔 *Lembrete de Agendamento - Zapby*\n\n";
        $message .= "Olá! Você tem um agendamento confirmado:\n\n";
        $message .= "👤 *Profissional:* {$professionalName}\n";
        
        if ($serviceName) {
            $message .= "💼 *Serviço:* {$serviceName}\n";
        }
        
        $message .= "📅 *Data/Hora:* {$dateTime}\n\n";
        $message .= "⚠️ Caso precise remarcar ou cancelar, entre em contato o quanto antes.\n\n";
        $message .= "_Mensagem automática - Zapby Pro_";

        $result = $this->sendMessage($phone, $message);

        return $result['success'] ?? false;
    }

    /**
     * Envia confirmação de agendamento
     */
    public function sendAppointmentConfirmation(
        string $phone,
        string $professionalName,
        string $dateTime,
        ?string $serviceName = null
    ): bool {
        $message = "✅ *Agendamento Confirmado - Zapby*\n\n";
        $message .= "Seu agendamento foi confirmado com sucesso!\n\n";
        $message .= "👤 *Profissional:* {$professionalName}\n";
        
        if ($serviceName) {
            $message .= "💼 *Serviço:* {$serviceName}\n";
        }
        
        $message .= "📅 *Data/Hora:* {$dateTime}\n\n";
        $message .= "Você receberá um lembrete próximo ao horário do agendamento.\n\n";
        $message .= "_Mensagem automática - Zapby Pro_";

        $result = $this->sendMessage($phone, $message);

        return $result['success'] ?? false;
    }

    /**
     * Desconecta sessão
     */
    public function logout(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->secretKey}",
                ])
                ->post("{$this->baseUrl}/api/{$this->session}/logout-session");

            Cache::forget("whatsapp_connected_{$this->session}");

            if ($response->successful()) {
                Log::info('WPPConnect: Sessão desconectada');
                return ['success' => true];
            }

            return ['success' => false];
        } catch (\Exception $e) {
            Log::error('WPPConnect: Erro ao desconectar', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retorna informações da sessão
     */
    public function getSessionInfo(): array
    {
        return [
            'session' => $this->session,
            'url' => $this->baseUrl,
            'connected' => $this->isConnected(),
        ];
    }
}
