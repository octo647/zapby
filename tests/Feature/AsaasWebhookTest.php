<?php

namespace Tests\Feature;

use App\Models\Professional;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsaasWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.asaas.webhook_token' => 'zapby-webhook-token']);
    }

    public function test_pix_deposit_without_subscription_is_acknowledged_and_does_not_change_a_subscription(): void
    {
        $subscription = $this->createSubscription(null, 'trial');

        $response = $this->withHeader('asaas-access-token', 'zapby-webhook-token')
            ->postJson('/webhooks/asaas', [
                'id' => 'evt_deposit',
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_deposit',
                    'subscription' => null,
                    'externalReference' => null,
                    'paymentLink' => null,
                    'billingType' => 'PIX',
                    'status' => 'RECEIVED',
                ],
            ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertSame('trial', $subscription->fresh()->status);
    }

    public function test_payment_is_only_applied_to_the_exact_non_null_subscription_id(): void
    {
        $unlinked = $this->createSubscription(null, 'trial');
        $linked = $this->createSubscription('sub_zapby_123', 'trial');

        $response = $this->withHeader('asaas-access-token', 'zapby-webhook-token')
            ->postJson('/webhooks/asaas', [
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_zapby_123',
                    'subscription' => 'sub_zapby_123',
                    'dueDate' => '2026-08-14',
                ],
            ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertSame('trial', $unlinked->fresh()->status);
        $this->assertSame('active', $linked->fresh()->status);
        $this->assertSame('2026-09-14', $linked->fresh()->next_due_date->toDateString());
    }

    public function test_invalid_token_is_acknowledged_without_processing_the_payment(): void
    {
        $subscription = $this->createSubscription('sub_zapby_protected', 'trial');

        $response = $this->withHeader('asaas-access-token', 'wrong-token')
            ->postJson('/webhooks/asaas', [
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_zapby_protected',
                    'subscription' => 'sub_zapby_protected',
                    'dueDate' => '2026-08-14',
                ],
            ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertSame('trial', $subscription->fresh()->status);
    }

    public function test_internal_processing_failure_is_logged_and_acknowledged(): void
    {
        $subscription = $this->createSubscription('sub_zapby_error', 'trial');

        $response = $this->withHeader('asaas-access-token', 'zapby-webhook-token')
            ->postJson('/webhooks/asaas', [
                'event' => 'PAYMENT_RECEIVED',
                'payment' => [
                    'id' => 'pay_zapby_error',
                    'subscription' => 'sub_zapby_error',
                    'dueDate' => ['invalid'],
                ],
            ]);

        $response->assertOk()->assertSeeText('OK');
        $this->assertSame('trial', $subscription->fresh()->status);
    }

    private function createSubscription(?string $asaasSubscriptionId, string $status): Subscription
    {
        $professional = Professional::create([
            'name' => 'Profissional '.uniqid(),
            'slug' => 'profissional-'.uniqid(),
            'cpf' => (string) random_int(10000000000, 99999999999),
            'whatsapp' => (string) random_int(10000000000, 99999999999),
            'profession' => 'Barbeiro',
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
            'password' => bcrypt('password'),
        ]);

        return Subscription::create([
            'professional_id' => $professional->id,
            'asaas_subscription_id' => $asaasSubscriptionId,
            'status' => $status,
            'amount' => 19.90,
        ]);
    }
}
