<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    protected $table = 'volumes';

    public $timestamps = false;

    protected $fillable = ['disco', 'volume'];
}
