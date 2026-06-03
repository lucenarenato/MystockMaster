<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /** Abre o Stripe Checkout para criar uma nova assinatura. */
    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'plan'             => ['required', 'string', 'in:' . implode(',', array_keys(config('plans')))],
        ]);

        $tenant  = Tenant::current();
        $plan    = $request->plan;
        $priceId = config("plans.{$plan}.price_id");

        abort_if(! $tenant || ! $priceId, 422, 'Plano sem preço Stripe configurado.');

        return $tenant->newSubscription('default', $priceId)
            ->trialDays((int) env('SAAS_TRIAL_DAYS', 14))
            ->checkout([
                'success_url' => route('billing.plans', ['checkout' => 'success', 'plan' => $plan]),
                'cancel_url' => route('billing.plans', ['checkout' => 'cancelled']),
                'metadata' => [
                    'tenant_id' => $tenant->id,
                    'plan' => $plan,
                ],
            ])
            ->redirect();
    }

    /** Troca o tenant para outro plano. */
    public function switchPlan(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'string', 'in:' . implode(',', array_keys(config('plans')))],
        ]);

        $tenant  = Tenant::current();
        $plan    = $request->plan;
        $priceId = config("plans.{$plan}.price_id");

        $tenant->subscription('default')->swap($priceId);

        $tenant->applyPlanLimits($plan);

        if (! $request->expectsJson()) {
            return redirect()->route('billing.plans')
                ->with('success', 'Plano atualizado com sucesso.');
        }

        return response()->json([
            'message' => 'Plano atualizado com sucesso.',
            'plan'    => $plan,
        ]);
    }

    /** Cancela a assinatura ao fim do ciclo atual. */
    public function cancel(Request $request): JsonResponse|RedirectResponse
    {
        Tenant::current()->subscription('default')->cancel();

        if (! $request->expectsJson()) {
            return redirect()->route('billing.plans')
                ->with('success', 'Assinatura cancelada. O acesso continua até o fim do período pago.');
        }

        return response()->json(['message' => 'Assinatura cancelada. O acesso continua até o fim do período pago.']);
    }

    /** Retorna a URL do portal de cobrança do Stripe. */
    public function portal(Request $request): RedirectResponse
    {
        return Tenant::current()->redirectToBillingPortal(
            route('home')
        );
    }

    /** Retorna os planos disponíveis e o plano atual do tenant. */
    public function plans(Request $request): View|JsonResponse
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

        if (! $request->expectsJson()) {
            return view('billing.plans', [
                'plans' => $plans,
                'tenant' => $tenant,
                'subscription' => $subscription,
                'subscribed' => $tenant?->subscribed('default') ?? false,
                'onTrial' => $tenant?->onTrial() ?? false,
            ]);
        }

        return response()->json([
            'plans'           => $plans,
            'subscribed'      => $tenant?->subscribed('default') ?? false,
            'on_trial'        => $tenant?->onTrial() ?? false,
            'trial_ends_at'   => $tenant?->trial_ends_at,
        ]);
    }
}
