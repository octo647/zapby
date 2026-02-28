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
            $phone = $this->formatPhone($this->appointment->client_whatsapp);
            
            Log::info('Sending WhatsApp reminder', [
                'appointment_id' => $this->appointment->id,
                'customer' => $this->appointment->client_name,
                'phone' => $phone,
            ]);

            // Caminho do arquivo (raiz de storage/app/)
            $filePath = storage_path('app/appointment_reminders.json');
            
            // Lê arquivo de lembretes existente ou cria novo
            $reminders = file_exists($filePath)
                ? json_decode(file_get_contents($filePath), true)
                : [];

            // Formata a mensagem do lembrete
            $message = $this->formatMessage();

            // Adiciona novo lembrete
            $reminders[] = [
                'phone' => $phone,
                'message' => $message,
            ];

            // Salva arquivo para o bot ler
            file_put_contents($filePath, json_encode($reminders, JSON_PRETTY_PRINT));

            Log::info('Reminder queued for WhatsApp bot', [
                'appointment_id' => $this->appointment->id,
                'total_reminders' => count($reminders),
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
     * Formata o telefone para WhatsApp (DDI + DDD + número sem formatação)
     */
    private function formatPhone(string $phone): string
    {
        // Remove tudo que não é número
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Se não tem DDI (55), adiciona
        if (strlen($phone) === 10 || strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        
        return $phone;
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
        
        if ($this->appointment->notes) {
            $message .= "📝 Observações: {$this->appointment->notes}\n\n";
        }
        
        if ($this->hoursBeforeAppointment <= 2) {
            $message .= "🚨 *Não se esqueça!* Seu horário está chegando.\n\n";
        }
        
        $message .= "Por favor, confirme sua presença ou avise caso precise remarcar.\n\n";
        $message .= "Até breve! 😊";
        
        return $message;
    }
}
