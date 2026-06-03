<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Setting;
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

            Tenant::setCurrent($tenant);
            $this->createDefaultSettings($tenant, $user);

            return $tenant;
        });
    }

    private function createDefaultSettings(Tenant $tenant, User $user): void
    {
        Setting::create([
            'company_name'              => $tenant->name,
            'company_email'             => $user->email,
            'company_phone'             => '',
            'company_logo'              => 'logo.png',
            'company_address'           => '',
            'company_tax'               => '',
            'default_currency_id'       => 1,
            'default_currency_position' => 'right',
            'default_date_format'       => 'd-m-Y',
            'default_language'          => 'pt-br',
            'is_rtl'                    => false,
            'sale_prefix'               => 'SA-',
            'saleReturn_prefix'         => 'SRE-',
            'purchase_prefix'           => 'PR-',
            'purchaseReturn_prefix'     => 'PRE-',
            'quotation_prefix'          => 'QU-',
            'salePayment_prefix'        => 'SP-',
            'purchasePayment_prefix'    => 'PP-',
            'show_email'                => true,
            'show_address'              => true,
            'show_order_tax'            => true,
            'show_discount'             => true,
            'show_shipping'             => true,
            'invoice_footer_text'       => 'Obrigado pela preferência!',
        ]);
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
