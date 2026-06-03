<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TenantLimitExceededException;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\Upload;
use App\Models\User;

class TenantLimits
{
    public const RESOURCES = [
        'users' => [
            'label' => 'Usuários',
            'limit' => 'max_users',
        ],
        'products' => [
            'label' => 'Produtos',
            'limit' => 'max_products',
        ],
        'sales' => [
            'label' => 'Vendas',
            'limit' => 'max_sales',
        ],
        'purchases' => [
            'label' => 'Compras',
            'limit' => 'max_purchases',
        ],
        'customers' => [
            'label' => 'Clientes',
            'limit' => 'max_customers',
        ],
        'suppliers' => [
            'label' => 'Fornecedores',
            'limit' => 'max_suppliers',
        ],
        'storage' => [
            'label' => 'Armazenamento',
            'limit' => 'max_storage_mb',
            'unit' => 'MB',
        ],
    ];

    /**
     * Lança TenantLimitExceededException se o tenant atingiu o limite do recurso.
     * Null no campo de limite significa ilimitado.
     * System admins (sem tenant) nunca são bloqueados.
     */
    public function check(string $resource): void
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return;
        }

        [$limitField, $counter] = $this->counterFor($resource, $tenant);

        if ($limitField === null) {
            return;
        }

        $limit = $tenant->{$limitField};

        if ($limit === null) {
            return;
        }

        if ($counter() >= $limit) {
            throw new TenantLimitExceededException($resource, $limit);
        }
    }

    public function usage(?Tenant $tenant = null): array
    {
        $tenant ??= Tenant::current();

        if (! $tenant) {
            return [];
        }

        return collect(self::RESOURCES)->map(function (array $meta, string $resource) use ($tenant) {
            [$limitField, $counter] = $this->counterFor($resource, $tenant);
            $limit = $limitField ? $tenant->{$limitField} : null;
            $used = $counter();
            $percentage = $limit ? min(100, (int) floor(($used / $limit) * 100)) : null;

            return [
                'resource' => $resource,
                'label' => $meta['label'],
                'used' => $used,
                'limit' => $limit,
                'unit' => $meta['unit'] ?? null,
                'percentage' => $percentage,
                'near_limit' => $percentage !== null && $percentage >= 80,
            ];
        })->values()->all();
    }

    public function nearLimitUsage(?Tenant $tenant = null): array
    {
        return array_values(array_filter(
            $this->usage($tenant),
            fn (array $item) => $item['near_limit'],
        ));
    }

    private function counterFor(string $resource, Tenant $tenant): array
    {
        return match ($resource) {
            'users'      => ['max_users',      fn () => User::where('tenant_id', $tenant->id)->count()],
            'products'   => ['max_products',   fn () => Product::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()],
            'sales'      => ['max_sales',      fn () => Sale::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()],
            'purchases'  => ['max_purchases',  fn () => Purchase::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()],
            'customers'  => ['max_customers',  fn () => Customer::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()],
            'suppliers'  => ['max_suppliers',  fn () => Supplier::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count()],
            'storage'    => ['max_storage_mb', fn () => (int) ceil((int) Upload::withoutGlobalScopes()->where('tenant_id', $tenant->id)->sum('size') / 1024 / 1024)],
            default      => [null, fn () => 0],
        };
    }
}
