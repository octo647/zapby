<?php

namespace Tests\Feature;

use App\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAgendaIndexingTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_professional_profile_returns_a_noindex_page_without_profile_data(): void
    {
        $professional = $this->createProfessional('suspended-professional', 'suspended');

        $response = $this->get(route('agenda.show', ['professional' => $professional->slug]));

        $response
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, follow')
            ->assertSee('name="robots" content="noindex,follow"', false)
            ->assertSeeText('Perfil temporariamente indisponível')
            ->assertDontSeeText($professional->name)
            ->assertDontSeeText($professional->profession)
            ->assertDontSeeText($professional->whatsapp);
    }

    public function test_active_professional_profile_remains_public_and_indexable(): void
    {
        $professional = $this->createProfessional('active-professional', 'active');

        $this->get(route('agenda.show', ['professional' => $professional->slug]))
            ->assertOk()
            ->assertSeeText($professional->name)
            ->assertDontSee('noindex', false)
            ->assertHeaderMissing('X-Robots-Tag');
    }

    public function test_sitemap_omits_inactive_profiles_and_keeps_active_profiles(): void
    {
        $inactive = $this->createProfessional('suspended-professional', 'suspended');
        $active = $this->createProfessional('active-professional', 'active');

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('agenda.show', ['professional' => $active->slug]), false)
            ->assertDontSee(route('agenda.show', ['professional' => $inactive->slug]), false);
    }

    public function test_unknown_professional_slug_still_returns_404(): void
    {
        $this->get('/slug-que-nao-existe')->assertNotFound();
    }

    private function createProfessional(string $slug, string $status): Professional
    {
        return Professional::create([
            'name' => "Profissional {$slug}",
            'slug' => $slug,
            'cpf' => (string) random_int(10000000000, 99999999999),
            'whatsapp' => (string) random_int(10000000000, 99999999999),
            'profession' => 'Barbeiro',
            'subscription_status' => $status,
            'trial_ends_at' => $status === 'trial' ? now()->addDays(7) : null,
            'password' => bcrypt('password'),
        ]);
    }
}
