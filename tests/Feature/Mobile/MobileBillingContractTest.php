<?php

namespace Tests\Feature\Mobile;

use App\Models\Subscription;
use App\Services\Billing\PaymentService;
use Tests\Feature\Mobile\Support\MobileApiTestCase;

class MobileBillingContractTest extends MobileApiTestCase
{
    public function test_subscriptions_cards_payment_and_cancel_contract(): void
    {
        $subscription = Subscription::factory()->active()->create();
        $user = $this->createVerifiedUser();
        $headers = $this->authHeaders($user);

        $this->getJson('/api/subscriptions')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'name', 'price', 'duration_days']]]);

        $this->getJson("/api/subscriptions/{$subscription->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['id', 'name', 'price', 'duration_days']]);

        $saveCard = $this->postJson('/api/payment/cards/save', [
            'card_number' => '4111111111111111',
            'card_holder' => 'MOBILE USER',
            'expiry_month' => '12',
            'expiry_year' => '2030',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['card_saved', 'card' => ['id', 'last_four']]]);

        $cardId = $saveCard->json('data.card.id');

        $this->getJson('/api/payment/cards', $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'card_holder', 'card_last_four', 'is_default']]]);

        $this->postJson("/api/payment/cards/{$cardId}/default", [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->mock(PaymentService::class, function ($mock) {
            $mock->shouldReceive('getCardData')->andReturn([
                'card_number' => '4111111111111111',
                'card_holder' => 'MOBILE USER',
                'expiry_month' => '12',
                'expiry_year' => '2030',
                'cvv' => '123',
                'is_saved_card' => false,
                'is_auto_payment' => false,
            ]);

            $mock->shouldReceive('processPayment')->andReturn([
                'success' => true,
                'transaction_id' => 'pay_contract_123',
                'message' => 'Платеж успешно обработан',
                'status' => 'completed',
                'amount' => 500,
                'processed_at' => now()->toDateTimeString(),
                'is_auto_payment' => false,
            ]);
        });

        $this->postJson('/api/payment/subscription', [
            'subscription_id' => $subscription->id,
            'save_card' => false,
            'use_saved_card' => false,
            'card_number' => '4111111111111111',
            'card_holder' => 'MOBILE USER',
            'expiry_month' => '12',
            'expiry_year' => '2030',
            'cvv' => '123',
        ], $headers)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['transaction_id', 'subscription', 'user_subscription', 'card_saved']]);

        $this->postJson('/api/cancel-subscription', [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->deleteJson("/api/payment/cards/{$cardId}", [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
