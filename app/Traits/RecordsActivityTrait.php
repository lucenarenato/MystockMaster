<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RecordsActivityTrait
{
    protected static function booted()
    {
        static::created(function (Model $model) {
            activity()->performedOn($model)->causedBy(Auth::user())->log('Criado');
        });

        static::updated(function (Model $model) {
            activity()->performedOn($model)->causedBy(Auth::user())->log('Atualizado');
        });

        static::deleted(function (Model $model) {
            activity()->performedOn($model)->causedBy(Auth::user())->log('Excluído');
        });
    }
}
