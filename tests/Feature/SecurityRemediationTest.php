<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_browser_payment_success_cannot_mark_an_invoice_paid(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tenant_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)->getJson('/payments/success?invoice_id='.$invoice->id.'&amount=1')
            ->assertOk();

        $this->assertSame('pending', $invoice->fresh()->status);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_valid_stripe_webhook_is_verified_and_idempotent(): void
    {
        config(['stripe.webhook_secret' => 'whsec_test']);
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tenant_id' => $user->id,
            'amount' => 125.50,
            'status' => 'pending',
        ]);

        $payload = json_encode([
            'id' => 'evt_test',
            'object' => 'event',
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_test',
                'object' => 'payment_intent',
                'status' => 'succeeded',
                'amount_received' => 12550,
                'currency' => 'usd',
                'metadata' => [
                    'invoice_id' => (string) $invoice->id,
                    'tenant_id' => (string) $user->id,
                ],
            ]],
        ], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');
        $header = "t={$timestamp},v1={$signature}";

        $this->call('POST', '/api/payments/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $header,
        ], $payload)->assertOk();
        $this->call('POST', '/api/payments/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $header,
        ], $payload)->assertOk();

        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame(1, Payment::where('payment_intent_id', 'pi_test')->count());
    }

    public function test_user_cannot_access_another_teams_property_media(): void
    {
        $ownerTeam = Team::factory()->create();
        $property = Property::factory()->create(['team_id' => $ownerTeam->id]);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->getJson("/api/properties/{$property->id}/images")
            ->assertForbidden();
    }

    public function test_chat_history_requires_the_conversation_secret(): void
    {
        $start = $this->postJson('/api/chatbot/start')->assertOk();
        $sessionId = $start->json('session_id');
        $accessToken = $start->json('access_token');

        $this->getJson("/api/chatbot/history/{$sessionId}")->assertForbidden();
        $this->withToken($accessToken)
            ->getJson("/api/chatbot/history/{$sessionId}")
            ->assertOk();

        $this->assertNotSame($accessToken, ChatConversation::first()->guest_token_hash);
    }
}
