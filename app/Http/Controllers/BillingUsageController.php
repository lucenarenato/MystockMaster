<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\TenantLimits;
use Illuminate\Support\Facades\Gate;

class BillingUsageController extends Controller
{
    public function __invoke(TenantLimits $limits)
    {
        abort_if(Gate::denies('setting_access'), 403);

        return view('billing.usage', [
            'tenant' => Tenant::current(),
            'usage' => $limits->usage(),
        ]);
    }
}

