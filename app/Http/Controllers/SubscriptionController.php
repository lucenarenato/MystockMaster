<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /** Cria uma nova assinatura para o tenant atual. */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan'             => ['required', 'string', 'in:' . implode(',', array_keys(config('plans')))],
            'payment_method'   => ['required', 'string'],
        ]);

        $tenant  = Tenant::current();
        $plan    = $request->plan;
        $priceId = config("plans.{$plan}.price_id");

        $tenant->createOrGetStripeCustomer();
        $tenant->updateDefaultPaymentMethod($request->payment_method);

        $tenant->newSubscription('default', $priceId)->create($request->payment_method);

        $tenant->applyPlanLimits($plan);

        return response()->json([
            'message' => 'Assinatura criada com sucesso.',
            'plan'    => $plan,
        ]);
    }

    /** Troca o tenant para outro plano. */
    public function switchPlan(Request $request): JsonResponse
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:' . implode(',', array_keys(config('plans')))],
        ]);

        $tenant  = Tenant::current();
        $plan    = $request->plan;
        $priceId = config("plans.{$plan}.price_id");

        $tenant->subscription('default')->swap($priceId);

        $tenant->applyPlanLimits($plan);

        return response()->json([
            'message' => 'Plano atualizado com sucesso.',
            'plan'    => $plan,
        ]);
    }

    /** Cancela a assinatura ao fim do ciclo atual. */
    public function cancel(): JsonResponse
    {
        Tenant::current()->subscription('default')->cancel();

        return response()->json(['message' => 'Assinatura cancelada. O acesso continua até o fim do período pago.']);
    }

    /** Retorna a URL do portal de cobrança do Stripe. */
    public function portal(Request $request): RedirectResponse
    {
        return Tenant::current()->redirectToBillingPortal(
            route('dashboard')
        );
    }

    /** Retorna os planos disponíveis e o plano atual do tenant. */
    public function plans(): JsonResponse
    {
        $tenant      = Tenant::current();
        $subscription = $tenant?->subscription('default');

        $plans = collect(config('plans'))->map(fn ($plan, $key) => [
            'key'       => $key,
            'name'      => $plan['name'],
            'price_id'  => $plan['price_id'],
            'limits'    => $plan['limits'],
            'active'    => $subscription?->stripe_price === $plan['price_id'],
        ]);

        return response()->json([
            'plans'           => $plans,
            'subscribed'      => $tenant?->subscribed('default') ?? false,
            'on_trial'        => $tenant?->onTrial() ?? false,
            'trial_ends_at'   => $tenant?->trial_ends_at,
        ]);
    }
}
