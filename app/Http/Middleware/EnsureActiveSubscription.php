<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // System admins e usuários sem tenant não são bloqueados.
        if (! $user || $user->is_system_admin || ! $user->tenant_id) {
            return $next($request);
        }

        $tenant = Tenant::current();

        if (! $tenant) {
            return $next($request);
        }

        if ($tenant->subscribed('default') || $tenant->onTrial()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Assinatura necessária para acessar este recurso.',
            ], 402);
        }

        return redirect()->route('billing.plans')
            ->with('warning', 'Ative ou renove sua assinatura para continuar.');
    }
}
