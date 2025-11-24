<?php

namespace App\Models;

use App\Traits\Auditavel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            // Sempre regenerar o ano_referencia com base na data_sepultamento
            $m->ano_referencia = $m->data_sepultamento
                ? Carbon::parse($m->data_sepultamento)->year
                : now()->year;

            // Sempre regenerar o número sequencial
            DB::transaction(function () use ($m) {
                // Pegar o último número sequencial, incluindo registros deletados
                $max = self::query()
                    ->where('empresa_id', $m->empresa_id)
                    ->where('ano_referencia', $m->ano_referencia)
                    ->withTrashed() // Incluir registros deletados
                    ->max('numero_sepultamento');

                $m->numero_sepultamento = (int) ($max ?? 0) + 1;
            });
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
    
    // Accessor para URL da certidão de óbito
    public function getCertidaoObitoUrlAttribute()
    {
        if ($this->certidao_obito_path) {
            // Se o caminho já contém o diretório certidoes, retorna diretamente
            if (str_starts_with($this->certidao_obito_path, 'certidoes/')) {
                return Storage::url($this->certidao_obito_path);
            }
            // Caso contrário, adiciona o diretório certidoes ao caminho
            return Storage::url('certidoes/' . $this->certidao_obito_path);
        }
        return null;
    }
}