<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {--hours=24 : Horas antes do agendamento}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembretes de agendamentos para clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $now = Carbon::now();
        
        $this->info("Buscando agendamentos para lembrete de {$hours}h antes...");
        
        // Buscar agendamentos que estão entre ($hours - 1h) e ($hours) à frente
        // Para 2h: buscar entre 1h e 2h à frente
        // Para 24h: buscar entre 23h e 24h à frente
        $startDateTime = $now->copy()->addHours($hours - 1);
        $endDateTime = $now->copy()->addHours($hours);

        $this->info("Janela de busca: " . $startDateTime->format('d/m/Y H:i') . " até " . $endDateTime->format('d/m/Y H:i'));

        // Buscar na mesma data
        $appointments = Appointment::where('date', $startDateTime->format('Y-m-d'))
            ->whereTime('time', '>=', $startDateTime->format('H:i:s'))
            ->whereTime('time', '<=', $endDateTime->format('H:i:s'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            SendAppointmentReminder::dispatch($appointment, $hours);
            $this->info("Lembrete enviado: {$appointment->client_name} - {$appointment->time}");
            $count++;
        }

        $this->info("✅ Enviados {$count} lembretes de agendamento ({$hours}h antes).");

        return Command::SUCCESS;
    }
}
