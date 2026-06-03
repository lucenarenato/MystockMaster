<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * Aplique em qualquer model para registrar criação, atualização e exclusão
 * automaticamente em audit_logs.
 */
trait RecordsActivity
{
    public static function bootRecordsActivity(): void
    {
        static::created(fn (Model $model) => self::writeLog('created', $model, [], $model->getAttributes()));

        static::updated(function (Model $model) {
            $changed = array_keys($model->getDirty());
            $old = array_intersect_key($model->getOriginal(), array_flip($changed));
            $new = array_intersect_key($model->getAttributes(), array_flip($changed));
            self::writeLog('updated', $model, $old, $new);
        });

        static::deleted(fn (Model $model) => self::writeLog('deleted', $model, $model->getAttributes(), []));
    }

    private static function writeLog(string $event, Model $model, array $old, array $new): void
    {
        $hidden   = $model->getHidden();
        $old      = array_diff_key($old, array_flip($hidden));
        $new      = array_diff_key($new, array_flip($hidden));

        AuditLog::create([
            'tenant_id'      => BelongsToTenant::tenantId(),
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->getKey(),
            'old_values'     => $old ?: null,
            'new_values'     => $new ?: null,
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
        ]);
    }
}
