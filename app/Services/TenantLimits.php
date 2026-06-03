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

        [$limitField, $counter] = match ($resource) {
            'users'      => ['max_users',      fn () => User::count()],
            'products'   => ['max_products',   fn () => Product::count()],
            'sales'      => ['max_sales',      fn () => Sale::count()],
            'purchases'  => ['max_purchases',  fn () => Purchase::count()],
            'customers'  => ['max_customers',  fn () => Customer::count()],
            'suppliers'  => ['max_suppliers',  fn () => Supplier::count()],
            'storage'    => ['max_storage_mb', fn () => (int) ceil(Upload::sum('size') / 1024 / 1024)],
            default      => [null, fn () => 0],
        };

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
}
