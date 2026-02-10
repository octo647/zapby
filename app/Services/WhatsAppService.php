<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $remindersFile;

    public function __construct()
    {
        $this->remindersFile = storage_path('app/appointment_reminders.json');
    }

    /**
     * Envia mensagem via WhatsApp (via arquivo JSON para o bot processar)
     */
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        try {
            // Formatar telefone para padrão internacional
            $phone = $this->formatPhoneNumber($phoneNumber);
            
            // Ler arquivo de lembretes existente
            $reminders = [];
            if (file_exists($this->remindersFile)) {
                $content = file_get_contents($this->remindersFile);
                if (!empty($content)) {
                    $reminders = json_decode($content, true) ?? [];
                }
            }
            
            // Adicionar nova mensagem
            $reminders[] = [
                'phone' => $phone,
                'message' => $message
            ];
            
            // Salvar arquivo
            file_put_contents(
                $this->remindersFile, 
                json_encode($reminders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            Log::info('WhatsApp message queued', [
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

    /**
     * Notifica profissional sobre novo agendamento
     */
    public function notifyNewAppointment($appointment): bool
    {
        $message = "🎉 *Novo agendamento!*\n\n";
        $message .= "👤 Cliente: {$appointment->client_name}\n";
        $message .= "📅 Data: " . \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') . "\n";
        $message .= "⏰ Horário: " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";
        $message .= "📱 WhatsApp: {$appointment->client_whatsapp}\n";

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

        return $this->sendMessage($appointment->client_whatsapp, $message);
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

        return $this->sendMessage($appointment->client_whatsapp, $message);
    }

    /**
     * Notifica cancelamento
     */
    public function notifyCancellation($appointment, string $toPhone): bool
    {
        $message = "❌ *Agendamento cancelado*\n\n";
        $message .= "📅 Data: " . \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') . "\n";
        $message .= "⏰ Horário: " . \Carbon\Carbon::parse($appointment->time)->format('H:i') . "\n";

        return $this->sendMessage($toPhone, $message);
    }
}

