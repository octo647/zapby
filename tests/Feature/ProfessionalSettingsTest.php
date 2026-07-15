<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckSubscription;
use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfessionalSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_rejects_a_whatsapp_number_registered_to_another_professional(): void
    {
        $existingProfessional = Professional::create([
            'name' => 'Profissional Existente',
            'slug' => 'profissional-existente',
            'cpf' => '12345678901',
            'whatsapp' => '3298294948',
            'profession' => 'Barbeiro',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
            'password' => bcrypt('password'),
        ]);

        $professional = Professional::create([
            'name' => 'Outro Profissional',
            'slug' => 'outro-profissional',
            'cpf' => '98765432100',
            'whatsapp' => '32911112222',
            'profession' => 'Manicure',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
            'password' => bcrypt('password'),
        ]);

        $this->withoutMiddleware(CheckSubscription::class);

        $response = $this
            ->actingAs($professional, 'professional')
            ->from(route('professional.settings'))
            ->post(route('professional.settings.update'), [
                'name' => $professional->name,
                'profession' => $professional->profession,
                'whatsapp' => '(32) 98294-948',
                'schedule_mode' => 'weekly',
            ]);

        $response
            ->assertRedirect(route('professional.settings'))
            ->assertSessionHasErrors('whatsapp');

        $this->assertSame(
            '32911112222',
            $professional->fresh()->whatsapp
        );
        $this->assertSame(
            '3298294948',
            $existingProfessional->fresh()->whatsapp
        );
    }
}
