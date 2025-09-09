<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditavel
{
    public static function bootAuditavel()
    {
        static::created(function ($model) {
            self::criarLog($model, 'create', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            self::criarLog($model, 'update', $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            self::criarLog($model, 'delete', $model->getOriginal(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                self::criarLog($model, 'restore', null, $model->getAttributes());
            });
        }
    }

    protected static function criarLog($model, string $acao, $antes, $depois): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'empresa_id' => Auth::user()->empresa_id ?? null,
                'tabela' => $model->getTable(),
                'registro_id' => $model->id ?? null,
                'acao' => $acao,
                'valores_antes' => $antes ?: null,
                'valores_depois' => $depois ?: null,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Opcional: logar no storage se der erro ao gravar auditoria
            report($e);
        }
    }
}
