<?php

declare(strict_types=1);

namespace App\Traits;

use App\Jobs\Middleware\SetTenantForJob;
use App\Models\Tenant;

/**
 * Apply to jobs to carry tenant context across queue boundaries.
 * Call captureCurrentTenant() at the end of the job's __construct().
 */
trait TenantAware
{
    public ?int $tenantId = null;

    public function captureCurrentTenant(): void
    {
        $this->tenantId = Tenant::current()?->id;
    }

    public function middleware(): array
    {
        return [new SetTenantForJob($this->tenantId)];
    }
}
