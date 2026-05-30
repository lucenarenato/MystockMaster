<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\HasAdvancedFilter;
use App\Traits\GetModelByUuid;
use App\Traits\UuidGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasAdvancedFilter;
    use HasFactory;
    use GetModelByUuid;
    use UuidGenerator;

    protected static ?self $currentTenant = null;

    public const ATTRIBUTES = [
        'id',
        'name',
        'slug',
        'ativo',
        'ordem',
        'created_at',
        'updated_at',
    ];

    public $orderable = self::ATTRIBUTES;
    public $filterable = self::ATTRIBUTES;

    protected $fillable = [
        'name',
        'slug',
        'descricao',
        'banner_id',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public static function current(): ?self
    {
        if (self::$currentTenant) {
            return self::$currentTenant;
        }

        if (auth()->check()) {
            return auth()->user()->tenant;
        }

        return null;
    }

    public static function currentId(): ?int
    {
        return self::current()?->id;
    }

    public static function setCurrent(?self $tenant): void
    {
        self::$currentTenant = $tenant;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'banner_id');
    }
}
