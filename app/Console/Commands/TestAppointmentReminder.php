<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;

class TestAppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:test-reminder {appointment_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de lembrete para um agendamento específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');

        if ($appointmentId) {
            $appointment = Appointment::find($appointmentId);
            
            if (!$appointment) {
                $this->error("Agendamento #{$appointmentId} não encontrado!");
                return Command::FAILURE;
            }
        } else {
            // Lista agendamentos disponíveis
            $appointments = Appointment::with('professional')
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('date')
                ->orderBy('time')
                ->get();

            if ($appointments->isEmpty()) {
                $this->error('Nenhum agendamento encontrado!');
                $this->info('Execute: php artisan db:seed --class=AppointmentSeeder');
                return Command::FAILURE;
            }

            $this->info('Agendamentos disponíveis:');
            $this->newLine();
            
            foreach ($appointments as $apt) {
                $this->line(sprintf(
                    '[%d] %s - %s às %s (%s) - %s',
                    $apt->id,
                    $apt->date->format('d/m/Y'),
                    $apt->time->format('H:i'),
                    $apt->client_name,
                    $apt->client_whatsapp,
                    $apt->status
                ));
            }

            $this->newLine();
            $selectedId = $this->ask('Digite o ID do agendamento para testar');
            
            $appointment = Appointment::find($selectedId);
            
            if (!$appointment) {
                $this->error("Agendamento #{$selectedId} não encontrado!");
                return Command::FAILURE;
            }
        }

        $this->info("Enviando lembrete para: {$appointment->client_name} ({$appointment->client_whatsapp})");
        
        // Dispara o job (vai executar imediatamente se QUEUE_CONNECTION=sync)
        SendAppointmentReminder::dispatch($appointment);
        
        $this->info('✅ Lembrete enviado para a fila!');
        $this->info('O bot WhatsApp processará nos próximos 30 segundos.');
        
        return Command::SUCCESS;
    }
}
