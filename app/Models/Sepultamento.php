<?php

namespace App\Models;

use App\Traits\Auditavel;
use Carbon\Carbon;
use DateTime;
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
            // Usar o atributo raw (banco de dados) em vez do valor formatado
            $rawDataSepultamento = $m->getAttributes()['data_sepultamento'] ?? null;
            $m->ano_referencia = $rawDataSepultamento
                ? Carbon::createFromFormat('Y-m-d', substr($rawDataSepultamento, 0, 10))->year
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

    /**
     * Converte data do formato brasileiro (dd/mm/yyyy) para ISO (yyyy-mm-dd) antes de salvar
     */
    public function setDataFalecimentoAttribute($value)
    {
        // Tratar valores vazios
        if (empty($value) || trim($value) === '') {
            $this->attributes['data_falecimento'] = null;
            return;
        }

        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
            $date = DateTime::createFromFormat('d/m/Y', $value);
            if ($date) {
                $this->attributes['data_falecimento'] = $date->format('Y-m-d');
            } else {
                $this->attributes['data_falecimento'] = $value;
            }
        } else {
            $this->attributes['data_falecimento'] = $value;
        }
    }

    /**
     * Converte data do formato brasileiro (dd/mm/yyyy) para ISO (yyyy-mm-dd) antes de salvar
     */
    public function setDataSepultamentoAttribute($value)
    {
        // Tratar valores vazios
        if (empty($value) || trim($value) === '') {
            $this->attributes['data_sepultamento'] = null;
            return;
        }

        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
            $date = DateTime::createFromFormat('d/m/Y', $value);
            if ($date) {
                $this->attributes['data_sepultamento'] = $date->format('Y-m-d');
            } else {
                $this->attributes['data_sepultamento'] = $value;
            }
        } else {
            $this->attributes['data_sepultamento'] = $value;
        }
    }

    /**
     * Converte data do formato ISO (yyyy-mm-dd) para brasileiro (dd/mm/yyyy) ao recuperar
     */
    public function getDataFalecimentoAttribute($value)
    {
        if ($value && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            $date = DateTime::createFromFormat('Y-m-d', substr($value, 0, 10));
            if ($date) {
                return $date->format('d/m/Y');
            }
        }
        return $value;
    }

    /**
     * Converte data do formato ISO (yyyy-mm-dd) para brasileiro (dd/mm/yyyy) ao recuperar
     */
    public function getDataSepultamentoAttribute($value)
    {
        if ($value && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            $date = DateTime::createFromFormat('Y-m-d', substr($value, 0, 10));
            if ($date) {
                return $date->format('d/m/Y');
            }
        }
        return $value;
    }
}
