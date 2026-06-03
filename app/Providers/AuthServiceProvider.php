<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Apenas o super-admin do sistema bypassa todos os gates.
        // Admins de tenant com role 'Super Admin' ficam restritos ao seu tenant.
        Gate::before(function ($user, $ability) {
            return $user->is_system_admin ? true : null;
        });
    }
}
