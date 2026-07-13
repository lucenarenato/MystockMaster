<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Price;
use Illuminate\Support\Facades\Log;


class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = [
            [
                'id' => 'basic',
                'name' => 'Básico',
                'price' => 10,
                'features' => ['3 usuários', '100 produtos', '300 vendas', '300 compras', '100 clientes', '50 fornecedores', '512 MB de armazenamento'],
            ],
            [
                'id' => 'pro',
                'name' => 'Pro',
                'price' => 20,
                'features' => ['10 usuários', '500 produtos', '2.000 vendas', '2.000 compras', '500 clientes', '200 fornecedores', '2 GB de armazenamento'],
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'price' => 30,
                'features' => ['Ilimitado usuários', 'Ilimitados produtos', 'Ilimitadas vendas', 'Ilimitadas compras', 'Ilimitados clientes', 'Ilimitados fornecedores', 'Ilimitado armazenamento'],
            ],
        ];

        return view('billing.plans', compact('plans'));
    }

    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $priceId = match ($request->input('plan_id')) {
            'basic' => env('STRIPE_PRICE_BASIC'),
            'pro' => env('STRIPE_PRICE_PRO'),
            'enterprise' => env('STRIPE_PRICE_ENTERPRISE'),
            default => throw new \Exception("Plano inválido"),
        };

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => 'MystockMaster - ' . ucfirst($request->input('plan_id')),
                        'metadata' => ['plan_id' => $request->input('plan_id')],
                    ],
                    'unit_amount' => (int) ($price * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription.index'),
            'trial_period_days' => 14, // Add this line to enable the trial period
        ]);

        return redirect($session->url, 303);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            abort(400, 'Invalid session ID');
        }

        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $user = auth()->user();
            $tenantId = $user->current_tenant_id; // Assuming you have this logic

            \App\Models\Tenant::find($tenantId)->applyPlanLimits($session->metadata['plan_id']);
        }

        return view('billing.success');
    }

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
