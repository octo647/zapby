<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $commandsFile;

    public function __construct()
    {
        $this->commandsFile = storage_path('app/whatsapp_commands.json');
    }

    /**
     * Envia mensagem via WhatsApp (via arquivo JSON para o bot processar)
     */
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        try {
            // Formatar telefone para padrão internacional
            $phone = $this->formatPhoneNumber($phoneNumber);

            if (!$this->isSendablePhone($phone)) {
                Log::warning('WhatsApp command not queued: invalid phone number', [
                    'phone' => $phoneNumber,
                    'formatted_phone' => $phone,
                ]);

                return false;
            }
            
            // Ler arquivo de comandos existente
            $commands = [];
            if (file_exists($this->commandsFile)) {
                $content = file_get_contents($this->commandsFile);
                if (!empty($content)) {
                    $commands = json_decode($content, true) ?? [];
                }
            }
            
            // Adicionar novo comando no formato esperado pelo bot
            $commands[] = [
                'type' => 'send_message',
                'to' => $phone,
                'message' => $message,
                'timestamp' => time()
            ];
            
            // Salvar arquivo
            file_put_contents(
                $this->commandsFile, 
                json_encode($commands, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            Log::info('WhatsApp command queued', [
                'phone' => $phone,
                'message' => substr($message, 0, 100)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Formatar número de telefone para padrão internacional
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove tudo que não é número
        $phone = preg_replace('/\D/', '', $phone);
        
        // Se não tem código do país, adiciona 55 (Brasil)
        if (strlen($phone) === 10 || strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        
        return $phone;
    }

    protected function isSendablePhone(?string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone ?? '');

        return str_starts_with($phone, '55') && strlen($phone) >= 12 && strlen($phone) <= 13;
    }

    protected function getAppointmentClientPhone($appointment): ?string
    {
        return $appointment->client_contact_phone;
    }

    /**
     * Notifica profissional sobre novo agendamento
     */
    public function notifyNewAppointment($appointment): bool
    {
        $message = "🎉 *Novo agendamento!*\n\n";
        $message .= "👤 Cliente: {$appointment->client_name}\n";
        $message .= "📅 Data: " . \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') . "\n";
        $message .= "⏰ Horário: " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";
        $message .= "📱 WhatsApp: " . ($this->getAppointmentClientPhone($appointment) ?? $appointment->client_whatsapp) . "\n";

        return $this->sendMessage($appointment->professional->whatsapp, $message);
    }

    /**
     * Lembra cliente do agendamento (24h antes)
     */
    public function remindClient($appointment): bool
    {
        $message = "👋 Olá {$appointment->client_name}!\n\n";
        $message .= "Lembrete do seu agendamento:\n";
        $message .= "📅 Amanhã às " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";
        $message .= "💈 Profissional: {$appointment->professional->name}\n\n";
        $message .= "Nos vemos lá! 😊";

        $phone = $this->getAppointmentClientPhone($appointment);

        return $phone ? $this->sendMessage($phone, $message) : false;
    }

    /**
     * Confirma agendamento ao cliente
     */
    public function confirmAppointmentToClient($appointment): bool
    {
        $message = "✅ *Agendamento confirmado!*\n\n";
        $message .= "📅 Data: " . \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') . "\n";
        $message .= "⏰ Horário: " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";
        $message .= "💈 Profissional: {$appointment->professional->name}\n\n";
        $message .= "Obrigado! 😊";

        $phone = $this->getAppointmentClientPhone($appointment);

        return $phone ? $this->sendMessage($phone, $message) : false;
    }

    /**
     * Notifica cancelamento
     */
    public function notifyCancellation($appointment, ?string $toPhone = null): bool
    {
        $message = "❌ *Agendamento cancelado*\n\n";
        $message .= "📅 Data: " . \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') . "\n";
        $message .= "⏰ Horário: " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";

        $phone = $this->isSendablePhone($this->formatPhoneNumber((string) $toPhone))
            ? $toPhone
            : $this->getAppointmentClientPhone($appointment);

        return $phone ? $this->sendMessage($phone, $message) : false;
    }

    /**
     * Notifica administrador sobre novo cadastro de profissional
     */
    public function notifyAdminNewProfessional($professional): bool
    {
        $adminWhatsApp = '5532999612957'; // WhatsApp do administrador
        
        $message = "🎉 *Novo Cadastro no ZapBy!*\n\n";
        $message .= "👤 Nome: {$professional->name}\n";
        $message .= "💼 Profissão: {$professional->profession}\n";
        $message .= "📱 WhatsApp: {$professional->whatsapp}\n";
        $message .= "🔑 CPF: " . $this->formatCpf($professional->cpf) . "\n";
        $message .= "🔗 Link: " . url('/agendar/' . $professional->slug) . "\n";
        $message .= "🔐 Access Key: {$professional->access_key}\n";
        $message .= "📅 Data: " . now()->format('d/m/Y H:i') . "\n";
        $message .= "⏰ Trial até: " . $professional->trial_ends_at->format('d/m/Y') . "\n";

        return $this->sendMessage($adminWhatsApp, $message);
    }

    /**
     * Formatar CPF para exibição
     */
    protected function formatCpf(string $cpf): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
}
