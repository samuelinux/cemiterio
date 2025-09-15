<?php

namespace App\Livewire\Empresa\Sepultamentos\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait WithAuditLogs
{
    private function logAcao(
        string $acao,
        ?int $registroId = null,
        ?array $antes = null,
        ?array $depois = null,
        string $tabela = 'sepultamentos'
    ): void {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'empresa_id' => Auth::user()->empresa_id ?? null,
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'acao' => $acao,
                'valores_antes' => $antes,
                'valores_depois' => $depois,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e); // não quebra UX se auditoria falhar
        }
    }
}
