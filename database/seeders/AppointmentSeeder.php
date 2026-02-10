<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega o primeiro profissional ou cria um se não existir
        $professional = Professional::first();
        
        if (!$professional) {
            $professional = Professional::create([
                'name' => 'Dr. Pedro Santos',
                'slug' => 'dr-pedro-santos',
                'whatsapp' => '32987654321',
                'profession' => 'Médico Clínico Geral',
                'default_duration' => 60,
                'working_days' => [1, 2, 3, 4, 5], // Seg a Sex
                'working_hours' => [
                    'start' => '08:00',
                    'end' => '18:00',
                ],
                'subscription_status' => 'active',
                'password' => \Illuminate\Support\Facades\Hash::make('senha123'),
            ]);
        }

        // Agendamentos de teste
        $appointments = [
            [
                'client_name' => 'João Silva',
                'client_whatsapp' => '(32) 98700-7302', // Seu número
                'date' => Carbon::tomorrow(),
                'time' => '14:00',
                'duration' => 60,
                'status' => 'confirmed',
                'notes' => 'Trazer exames anteriores',
            ],
            [
                'client_name' => 'Maria Santos',
                'client_whatsapp' => '32987001234',
                'date' => Carbon::tomorrow(),
                'time' => '15:30',
                'duration' => 45,
                'status' => 'pending',
                'notes' => null,
            ],
            [
                'client_name' => 'Carlos Oliveira',
                'client_whatsapp' => '32987005678',
                'date' => Carbon::today()->addDays(2),
                'time' => '10:00',
                'duration' => 30,
                'status' => 'confirmed',
                'notes' => 'Retorno',
            ],
            [
                'client_name' => 'Ana Paula',
                'client_whatsapp' => '32987009999',
                'date' => Carbon::today(),
                'time' => '16:00',
                'duration' => 60,
                'status' => 'confirmed',
                'notes' => 'Primeira consulta',
            ],
        ];

        foreach ($appointments as $data) {
            Appointment::create([
                'professional_id' => $professional->id,
                'client_name' => $data['client_name'],
                'client_whatsapp' => $data['client_whatsapp'],
                'date' => $data['date'],
                'time' => $data['time'],
                'duration' => $data['duration'],
                'status' => $data['status'],
                'notes' => $data['notes'],
            ]);
        }

        $this->command->info('Criados ' . count($appointments) . ' agendamentos de teste!');
    }
}
