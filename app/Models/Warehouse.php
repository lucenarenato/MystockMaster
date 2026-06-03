<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\BelongsToTenant;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasAdvancedFilter;
    use BelongsToTenant;
    use RecordsActivity;

    public const ATTRIBUTES = [
        'id',
        'tenant_id',
        'name',
        'city',
        'phone',
        'email',
        'country',
        'created_at',
        'updated_at',
    ];

    public $orderable = self::ATTRIBUTES;
    public $filterable = self::ATTRIBUTES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name', 'phone', 'country', 'city', 'email',
    ];

    /** @return BelongsToMany<User> */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /** @return BelongsToMany<Product> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_warehouse', 'warehouse_id', 'product_id')
            ->withPivot('price', 'cost', 'qty');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->products->sum('pivot.qty');
    }

    public function getStockValueAttribute()
    {
        return $this->products->reduce(
            fn ($value, $product) => $value + ($product->pivot->qty * $product->pivot->cost),
            0,
        );
    }
}
