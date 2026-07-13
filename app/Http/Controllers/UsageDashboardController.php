<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Services\UsageService;

class UsageDashboardController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->current_tenant;
        $usage = app(UsageService::class)->getUsage($tenant);

        return view('dashboard.usage', compact('tenant', 'usage'));
    }
}
