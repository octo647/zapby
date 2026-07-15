<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Verificar assinaturas expiradas diariamente às 8h da manhã
Schedule::command('subscriptions:check-expired')
    ->dailyAt('08:00')
    ->timezone('America/Sao_Paulo');

// Enviar lembretes de agendamentos - 24h antes (diariamente às 9h)
Schedule::command('appointments:send-reminders --hours=24')
    ->dailyAt('09:00')
    ->timezone('America/Sao_Paulo');

// Enviar lembretes de agendamentos - 2h antes (a cada hora)
Schedule::command('appointments:send-reminders --hours=2')
    ->hourly()
    ->timezone('America/Sao_Paulo');
