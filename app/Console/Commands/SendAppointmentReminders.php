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
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembretes de agendamentos para clientes (24h antes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $appointments = Appointment::where('date', $tomorrow)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            SendAppointmentReminder::dispatch($appointment);
            $count++;
        }

        $this->info("Enviados {$count} lembretes de agendamento.");

        return Command::SUCCESS;
    }
}
