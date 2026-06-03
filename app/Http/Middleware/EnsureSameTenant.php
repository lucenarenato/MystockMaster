<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\BelongsToTenant;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSameTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->is_system_admin) {
            return $next($request);
        }

        foreach ($request->route()->parameters() as $parameter) {
            if (! ($parameter instanceof Model)) {
                continue;
            }

            if (! in_array(BelongsToTenant::class, class_uses_recursive($parameter), true)) {
                continue;
            }

            if ((int) $parameter->tenant_id !== (int) $user->tenant_id) {
                abort(403, 'Acesso negado: recurso pertence a outro tenant.');
            }
        }

        return $next($request);
    }
}
