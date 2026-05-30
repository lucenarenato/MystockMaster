<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerGroup extends Model
{
    use HasAdvancedFilter;
    use BelongsToTenant;
    use HasFactory;

    public $orderable = [
        'id', 'tenant_id', 'name', 'percentage', 'status',
    ];

    public $filterable = [
        'id', 'tenant_id', 'name', 'percentage', 'status',
    ];

    protected $fillable = [
        'tenant_id',
        'name',
        'percentage',
        'status',
    ];
}
