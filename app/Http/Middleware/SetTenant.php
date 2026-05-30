<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetTenant
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $tenant = Auth::user()->tenant;

            if ($tenant) {
                Tenant::setCurrent($tenant);
            }
        }

        return $next($request);
    }
}
