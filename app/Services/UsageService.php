<?php

namespace App\Services;

use App\Models\Tenant;
use Carbon\Carbon;
use App\Events\TenantLimitReached;

class UsageService
{
    public function getUsage(Tenant $tenant)
    {
        $usage = [
            'max_products' => count($tenant->products),
            'max_vendas' => count($tenant->sales),
            'max_compras' => count($tenant->purchases),
            // Add other usage metrics here
        ];

        foreach ($usage as $limit => $value) {
            if ($value >= $tenant->getPlanLimits()[$limit]) {
                event(new TenantLimitReached($tenant, $limit));
            }
        }

        return $usage;
    }
}
