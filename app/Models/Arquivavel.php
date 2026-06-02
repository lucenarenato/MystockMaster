<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arquivavel extends Model
{
    protected $table = 'arquivaveis';

    protected $guarded = [];

    /**
     * Get the related Arquivo record.
     */
    public function arquivo()
    {
        return $this->belongsTo(Arquivo::class, 'arquivo_id');
    }
}
