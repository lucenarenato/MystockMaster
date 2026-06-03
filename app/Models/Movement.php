<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MovementType;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'type',
        'quantity',
        'price',
        'date',
        'movable_id',
        'movable_type',
        'user_id',
    ];

    protected $casts = [
        'type' => MovementType::class,
    ];

    public function movable()
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'user_id',
        );
    }

    public function productWarehouse()
    {
        return $this->belongsTo(ProductWarehouse::class);
    }
}
