<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use App\Models\Tenant;

class SetTenantForJob
{
    public function __construct(private readonly ?int $tenantId) {}

    public function handle(object $job, callable $next): void
    {
        if ($this->tenantId) {
            Tenant::setCurrent(Tenant::find($this->tenantId));
        }

        try {
            $next($job);
        } finally {
            Tenant::setCurrent(null);
        }
    }
}
