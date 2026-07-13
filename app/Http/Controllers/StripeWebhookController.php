<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    public function handleCustomerSubscriptionCreated(array $payload): void
    {
        parent::handleCustomerSubscriptionCreated($payload);

        $this->applyLimitsFromSubscriptionPayload($payload);
    }

    /**
     * Atualiza os limites do tenant quando a assinatura muda de plano
     * via portal do Stripe (upgrade/downgrade feito diretamente lá).
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

        $this->applyLimitsFromSubscriptionPayload($payload);
    }

    private function applyLimitsFromSubscriptionPayload(array $payload): void
    {
        $stripeSubscription = $payload['data']['object'];
        $stripeId           = $stripeSubscription['customer'];
        $priceId            = $stripeSubscription['items']['data'][0]['price']['id'] ?? null;

        if (! $priceId) {
            return;
        }

        $tenant = Tenant::where('stripe_id', $stripeId)->first();

        if (! $tenant) {
            return;
        }

        $planKey = $this->planKeyFromPriceId($priceId);

        if ($planKey) {
            $tenant->applyPlanLimits($planKey);
        }
    }

    private function planKeyFromPriceId(string $priceId): ?string
    {
        foreach (config('plans') as $key => $plan) {
            if ($plan['price_id'] === $priceId) {
                return $key;
            }
        }

        return null;
    }

    public function handle(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Event::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $subscription = $event->data->object->subscriptions->data[0];
                $planId = $subscription->items->data[0]->plan->id;

                // Capture the tenant ID from some context (e.g., user session)
                $tenantId = auth()->user()->current_tenant_id;

                \App\Models\Tenant::find($tenantId)->applyPlanLimits($planId);
                break;
            // Handle other events as needed
        }

        return response()->json(['received' => 'success']);
    }
}
