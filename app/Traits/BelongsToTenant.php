<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void addGlobalScope(string $identifier, \Closure $scope)
 * @method static void creating(\Closure $callback)
 * @method BelongsTo belongsTo(string $related, string $foreignKey = null, string $ownerKey = null, string $relation = null)
 * @method string getTable()
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            $tenantId = self::tenantId();

            if (! $tenantId && app()->runningInConsole()) {
                return;
            }

            if (! $tenantId) {
                $builder->whereNull($builder->getModel()->getTable() . '.tenant_id');

                return;
            }

            $builder->where(
                $builder->getModel()->getTable() . '.tenant_id',
                $tenantId,
            );
        });

        static::creating(function (Model $model) {
            if (! $model->tenant_id) {
                $model->tenant_id = self::tenantId();
            }
        });
    }

    public static function tenantId(): ?int
    {
        $currentTenant = Tenant::current();

        if ($currentTenant) {
            return $currentTenant->id;
        }

        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }

        return null;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope query to a specific tenant.
     *
     * @param  \\Illuminate\\Database\\Eloquent\\Builder  $query
     * @param  mixed  $tenant
     */
    public function scopeForTenant(Builder $query, $tenant)
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        if (! $tenantId) {
            return $query;
        }

        return $query->where($this->getTable().'.tenant_id', $tenantId);
    }
}
