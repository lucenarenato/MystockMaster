<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProvisionTenant
{
    /**
     * Cria um Tenant para o usuário recém-registrado, linka o tenant_id
     * e atribui o role de admin do tenant.
     * Tudo dentro de uma transaction — se qualquer etapa falhar, o usuário
     * não fica sem tenant.
     */
    public function handle(User $user, ?string $companyName = null): Tenant
    {
        return DB::transaction(function () use ($user, $companyName) {
            $name = $companyName ?: $user->name;
            $slug = $this->uniqueSlug($name);

            $tenant = Tenant::create([
                'name'  => $name,
                'slug'  => $slug,
                'ativo' => true,
                'ordem' => 0,
            ]);

            $user->update(['tenant_id' => $tenant->id]);

            $user->assignRole('Super Admin');

            return $tenant;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Tenant::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
