<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Atualiza os limites do tenant quando a assinatura muda de plano
     * via portal do Stripe (upgrade/downgrade feito diretamente lá).
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

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
}
