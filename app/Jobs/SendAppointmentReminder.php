<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendAppointmentReminder implements ShouldQueue
{
    use Queueable;

    private const TIMEZONE = 'America/Sao_Paulo';

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment,
        public int $hoursBeforeAppointment = 24
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $now = now(self::TIMEZONE);

            // Verificar se já enviamos este lembrete hoje
            $cacheKey = "reminder_sent_{$this->appointment->id}_{$this->hoursBeforeAppointment}h_" . $now->format('Y-m-d');
            
            if (cache()->has($cacheKey)) {
                Log::info('Reminder already sent today', [
                    'appointment_id' => $this->appointment->id,
                    'hours' => $this->hoursBeforeAppointment,
                ]);
                return;
            }
            
            $phone = $this->appointment->client_contact_phone;

            if (!$phone) {
                Log::warning('Skipping WhatsApp reminder: appointment has no valid client phone', [
                    'appointment_id' => $this->appointment->id,
                    'client_whatsapp' => $this->appointment->client_whatsapp,
                    'phone_number' => $this->appointment->phone_number,
                ]);

                return;
            }
            
            Log::info('Sending WhatsApp reminder', [
                'appointment_id' => $this->appointment->id,
                'customer' => $this->appointment->client_name,
                'phone' => $phone,
                'hours_before' => $this->hoursBeforeAppointment,
            ]);

            // Usar o arquivo de comandos que o bot já processa
            $filePath = storage_path('app/whatsapp_commands.json');
            
            // Lê arquivo de comandos existente ou cria novo
            $commands = file_exists($filePath)
                ? json_decode(file_get_contents($filePath), true)
                : [];

            // Formata a mensagem do lembrete
            $message = $this->formatMessage();

            // Adiciona comando para enviar mensagem
            $commands[] = [
                'type' => 'send_message',
                'to' => $phone,
                'message' => $message,
            ];

            // Salva arquivo para o bot ler
            file_put_contents($filePath, json_encode($commands, JSON_PRETTY_PRINT));

            // Marcar como enviado (expira em 25 horas)
            cache()->put($cacheKey, true, $now->copy()->addHours(25));

            Log::info('Reminder queued for WhatsApp bot', [
                'appointment_id' => $this->appointment->id,
                'total_commands' => count($commands),
            ]);

        } catch (\Exception $e) {
            Log::error('Error queueing WhatsApp reminder', [
                'appointment_id' => $this->appointment->id,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Formata a mensagem do lembrete
     */
    private function formatMessage(): string
    {
        $professionalName = $this->appointment->professional->name ?? 'Profissional';
        $dateObj = is_string($this->appointment->date) ? \Carbon\Carbon::parse($this->appointment->date) : $this->appointment->date;
        $timeObj = is_string($this->appointment->time) ? \Carbon\Carbon::parse($this->appointment->time) : $this->appointment->time;
        $date = $dateObj->format('d/m/Y');
        $time = $timeObj->format('H:i');
        $duration = $this->appointment->duration;
        
        // Mensagem diferente dependendo do tempo
        if ($this->hoursBeforeAppointment <= 2) {
            $message = "⏰ *Lembrete: Seu agendamento é daqui a {$this->hoursBeforeAppointment} horas!*\n\n";
        } else {
            $message = "🗓️ *Lembrete de Agendamento*\n\n";
        }
        
        $message .= "Olá *{$this->appointment->client_name}*!\n\n";
        $message .= "Você tem um agendamento marcado:\n\n";
        $message .= "👤 Profissional: *{$professionalName}*\n";
        $message .= "📅 Data: *{$date}*\n";
        $message .= "⏰ Horário: *{$time}*\n";
        $message .= "⏱️ Duração: *{$duration} minutos*\n\n";
        
        $notes = trim((string) $this->appointment->notes);

        if ($notes !== '' && strcasecmp($notes, 'Agendamento via WhatsApp Bot') !== 0) {
            $message .= "📝 Observações: {$notes}\n\n";
        }
        
        if ($this->hoursBeforeAppointment <= 2) {
            $message .= "🚨 *Não se esqueça!* Seu horário está chegando.\n\n";
        }
        
        $message .= "Por favor, avise caso precise remarcar.\n\n";
        $message .= "Até breve! 😊";
        
        return $message;
    }
}
