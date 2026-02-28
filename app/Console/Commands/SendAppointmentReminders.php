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
        $targetDateTime = Carbon::now()->addHours($hours);
        
        $this->info("Buscando agendamentos para aproximadamente {$hours} horas à frente...");
        $this->info("Data/hora alvo: " . $targetDateTime->format('d/m/Y H:i'));

        // Buscar agendamentos na data/hora alvo (com margem de 1 hora)
        $startTime = $targetDateTime->copy()->subMinutes(30);
        $endTime = $targetDateTime->copy()->addMinutes(30);

        $appointments = Appointment::where('date', $targetDateTime->format('Y-m-d'))
            ->whereTime('time', '>=', $startTime->format('H:i:s'))
            ->whereTime('time', '<=', $endTime->format('H:i:s'))
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
