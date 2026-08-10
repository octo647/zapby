<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckSubscription;
use App\Models\Appointment;
use App\Models\Professional;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfessionalDashboardScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_dashboard_shows_free_occupied_and_blocked_slots_for_today(): void
    {
        Carbon::setTestNow('2026-08-10 08:00:00');
        $professional = $this->professional();
        $professional->workPeriods()->create([
            'weekday' => 1,
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);
        Appointment::create([
            'professional_id' => $professional->id,
            'client_name' => 'Ana',
            'client_whatsapp' => '5532999999999',
            'date' => '2026-08-10',
            'time' => '09:30',
            'duration' => 30,
            'status' => 'confirmed',
        ]);
        $professional->timeBlocks()->create([
            'date' => '2026-08-10',
            'start_time' => '10:00',
            'end_time' => '10:30',
            'reason' => 'Pausa',
        ]);

        $this->withoutMiddleware(CheckSubscription::class);
        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.dashboard'));

        $response->assertOk()
            ->assertSee('Horários de hoje')
            ->assertSee('value="09:00"', false)
            ->assertSee('10:00 — Bloqueado: Pausa')
            ->assertSee('09:30 — Ocupado: Ana');
    }

    public function test_dashboard_translates_appointment_status_and_upcoming_month(): void
    {
        Carbon::setTestNow('2026-08-31 08:00:00');
        $professional = $this->professional();
        Appointment::create([
            'professional_id' => $professional->id,
            'client_name' => 'Ana',
            'client_whatsapp' => '5532999999999',
            'date' => '2026-08-31',
            'time' => '09:00',
            'duration' => 30,
            'service' => 'Corte de cabelo',
            'status' => 'pending',
        ]);
        Appointment::create([
            'professional_id' => $professional->id,
            'client_name' => 'Bruno',
            'client_whatsapp' => '5532888888888',
            'date' => '2026-09-01',
            'time' => '10:00',
            'duration' => 30,
            'status' => 'confirmed',
        ]);

        $this->withoutMiddleware(CheckSubscription::class);
        $response = $this->actingAs($professional, 'professional')
            ->get(route('professional.dashboard'));

        $response->assertOk()
            ->assertSee('Pendente')
            ->assertSee('Corte de cabelo')
            ->assertDontSee('30 minutos')
            ->assertSee('set');
    }

    public function test_professional_can_block_a_free_slot_from_dashboard(): void
    {
        Carbon::setTestNow('2026-08-10 08:00:00');
        $professional = $this->professional();
        $this->withoutMiddleware(CheckSubscription::class);

        $response = $this->actingAs($professional, 'professional')
            ->from(route('professional.dashboard'))
            ->post(route('professional.time-blocks.store'), [
                'date' => '2026-08-10',
                'block_type' => 'partial',
                'start_time' => '09:00',
                'end_time' => '09:30',
                'reason' => 'Bloqueado pelo painel',
            ]);

        $response->assertRedirect(route('professional.dashboard'));
        $this->assertDatabaseHas('time_blocks', [
            'professional_id' => $professional->id,
            'date' => '2026-08-10',
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
        ]);
    }

    public function test_professional_cannot_block_over_an_appointment(): void
    {
        Carbon::setTestNow('2026-08-10 08:00:00');
        $professional = $this->professional();
        Appointment::create([
            'professional_id' => $professional->id,
            'client_name' => 'Ana',
            'client_whatsapp' => '5532999999999',
            'date' => '2026-08-10',
            'time' => '09:00',
            'duration' => 30,
            'status' => 'confirmed',
        ]);
        $this->withoutMiddleware(CheckSubscription::class);

        $response = $this->actingAs($professional, 'professional')
            ->from(route('professional.dashboard'))
            ->post(route('professional.time-blocks.store'), [
                'date' => '2026-08-10',
                'block_type' => 'partial',
                'start_time' => '09:00',
                'end_time' => '09:30',
            ]);

        $response->assertRedirect(route('professional.dashboard'))
            ->assertSessionHasErrors('start_time');
        $this->assertDatabaseCount('time_blocks', 0);
    }

    private function professional(): Professional
    {
        return Professional::create([
            'name' => 'Profissional Teste',
            'slug' => 'profissional-teste',
            'cpf' => '12345678901',
            'whatsapp' => '32999999999',
            'profession' => 'Barbeiro',
            'schedule_mode' => 'weekly',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
            'password' => bcrypt('password'),
        ]);
    }
}
