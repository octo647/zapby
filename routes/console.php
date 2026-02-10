<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Enviar lembretes de agendamentos diariamente às 9h da manhã
Schedule::command('appointments:send-reminders')
    ->dailyAt('09:00')
    ->timezone('America/Sao_Paulo');
