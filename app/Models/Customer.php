<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\BelongsToTenant;
use App\Traits\RecordsActivity;
use App\Traits\GetModelByUuid;
use App\Traits\UuidGenerator;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Customer extends Model
{
    use HasAdvancedFilter;
    use BelongsToTenant;
    use RecordsActivity;
    use GetModelByUuid;
    use UuidGenerator;
    use HasFactory;

    public const ATTRIBUTES = [
        'id',
        'tenant_id',
        'name',
        'email',
        'phone',
        'city',
        'country',
        'address',
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
        'uuid',
        'id',
        'tenant_id',
        'city',
        'tax_number',
        'name',
        'email',
        'phone',
        'country',
        'address',
    ];

    /** @return HasOne<Wallet> */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /** @return HasMany<Sale> */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSalesAttribute()
    {
        return $this->customerSum('total_amount', Sale::class);
    }

    public function getTotalSaleReturnsAttribute(): int|float
    {
        return $this->customerSum('total_amount', SaleReturn::class);
    }

    public function getTotalPaymentsAttribute(): int|float
    {
        return $this->customerSum('paid_amount', Sale::class);
    }

    public function getTotalDueAttribute(): int|float
    {
        return $this->customerSum('due_amount', Sale::class);
    }

    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'arquivavel');
    }

    private function customerSum($column, $model)
    {
        return $model::where('customer_id', $this->id)->sum($column);
    }
}
