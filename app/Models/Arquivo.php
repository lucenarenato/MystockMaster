<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Arquivo extends Model
{
    use BelongsToTenant;

    protected $table = 'arquivos';

    protected $guarded = [];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $appends = ['url', 'src'];

    public function arquivoRelacionamentos()
    {
        return $this->hasMany(Arquivavel::class, 'arquivo_id');
    }

    public function getUrlAttribute(): ?string
    {
        if (! empty($this->sha256)) {
            return config('app.url') . '/storage/' . $this->sha256;
        }

        return null;
    }

    public function getSrcAttribute(): string
    {
        $src = $this->url ?? '';

        if (($this->extensao ?? '') === 'pdf') {
            $src = '/images/pdf2.png';
        } elseif (in_array(($this->extensao ?? ''), ['mp4', 'avi'], true)) {
            $src = '/images/video_thumbnail.jpg';
        }

        return $src;
    }

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class, 'volume_id');
    }

    public function getCaminho(): string
    {
        if ($this->unidade && $this->volume) {
            $tenantSegment = $this->tenant_id ? 'tenant/' . $this->tenant_id . '/' : '';

            return $tenantSegment . $this->unidade . '/' . $this->volume->volume . '/' . $this->id . '.' . ($this->extensao ?? '');
        }

        return (string) ($this->caminho ?? '');
    }

    public function getBinary()
    {
        if ($this->volume) {
            return Storage::disk($this->volume->disco)->get($this->getCaminho());
        }

        return null;
    }

    public function removerArquivo(): bool
    {
        $statusDelete = $this->delete();

        if (! $statusDelete) {
            return false;
        }

        if ($this->volume) {
            return Storage::disk($this->volume->disco)->delete($this->getCaminho());
        }

        return false;
    }
}
