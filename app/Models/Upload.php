<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Upload extends Model implements HasMedia
{
    use BelongsToTenant;
    use InteractsWithMedia;

    protected $guarded = [];

    /**
     * Map the Upload model to the existing Portuguese-named files table.
     */
    protected $table = 'arquivos';

    public function arquivavel(): MorphTo
    {
        return $this->morphTo();
    }
}
