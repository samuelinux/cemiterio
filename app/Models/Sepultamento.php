<?php

namespace App\Models;

use App\Traits\Auditavel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sepultamento extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Auditavel;

    protected $fillable = [
        'empresa_id',
        'user_id',
        'nome_falecido',
        'mae',
        'pai',
        'indigente',
        'natimorto',
        'translado',
        'membro',
        'data_falecimento',
        'data_sepultamento',
        'quadra',
        'fila',
        'cova',
        'ano_referencia',
        'numero_sepultamento',
        'certidao_obito_path',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'indigente' => 'boolean',
        'natimorto' => 'boolean',
        'translado' => 'boolean',
        'membro' => 'boolean',
        'data_falecimento' => 'date',
        'data_sepultamento' => 'date',
        'ativo' => 'boolean',
    ];

    // Relações
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function causas()
    {
        return $this->belongsToMany(CausaMorte::class, 'sepultamento_causa', 'sepultamento_id', 'causa_morte_id')
                    ->withTimestamps();
    }

    protected static function booted(): void
    {
        // Geração do ano + número sequencial por empresa/ano
        static::creating(function (Sepultamento $m) {
            // Define ano_ref a partir de data_sepultamento (ou ano atual se não houver)
            if (empty($m->ano_referencia)) {
                $m->ano_referencia = $m->data_sepultamento
                    ? Carbon::parse($m->data_sepultamento)->year
                    : now()->year;
            }

            // Se não veio número, gera o próximo disponível
            if (empty($m->numero_sepultamento)) {
                DB::transaction(function () use ($m) {
                    $max = self::query()
                        ->where('empresa_id', $m->empresa_id)
                        ->where('ano_referencia', $m->ano_referencia)
                        ->lockForUpdate()
                        ->max('numero_sepultamento');

                    $m->numero_sepultamento = (int) ($max ?? 0) + 1;
                });
            }
        });
    }

    // Helper de exibição (UI): 2025-000123
    public function numeroFormatado(): string
    {
        return sprintf('%d-%06d', $this->ano_referencia, $this->numero_sepultamento);
    }

    // App\Models\Sepultamento.php
    public function scopePorEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
