<?php

namespace Database\Seeders;

use App\Models\Professional;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar profissional de exemplo
        $professional = Professional::create([
            'name' => 'João Silva',
            'slug' => 'joao-silva',
            'whatsapp' => '11999999999',
            'profession' => 'Cabeleireiro',
            'default_duration' => 60,
            'working_days' => [1, 2, 3, 4, 5, 6], // Seg a Sáb
            'working_hours' => [
                'start' => '08:00',
                'end' => '19:00',
            ],
            'subscription_status' => 'trial',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'password' => Hash::make('senha123'),
        ]);

        // Criar assinatura
        Subscription::create([
            'professional_id' => $professional->id,
            'status' => 'trial',
            'amount' => 19.90,
        ]);

        $this->command->info('Profissional de exemplo criado!');
        $this->command->info('Login: joao-silva');
        $this->command->info('Senha: senha123');
    }
}
