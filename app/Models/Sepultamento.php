<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sepultamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'user_id',
        'nome_falecido',
        'cpf_falecido',
        'data_nascimento',
        'data_falecimento',
        'causa_morte',
        'naturalidade',
        'profissao',
        'estado_civil',
        'sexo',
        'data_sepultamento',
        'hora_sepultamento',
        'local_sepultamento',
        'quadra',
        'gaveta',
        'numero_sepultura',
        'tipo_sepultamento',
        'nome_responsavel',
        'cpf_responsavel',
        'telefone_responsavel',
        'parentesco',
        'numero_certidao_obito',
        'cartorio_certidao',
        'numero_declaracao_obito',
        'observacoes'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_falecimento' => 'date',
        'data_sepultamento' => 'date',
        'hora_sepultamento' => 'datetime',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_sepultamento', [$dataInicio, $dataFim]);
    }

    // Accessors
    public function getNomeCompletoAttribute()
    {
        return $this->nome_falecido;
    }

    public function getIdadeAttribute()
    {
        if ($this->data_nascimento && $this->data_falecimento) {
            return $this->data_nascimento->diffInYears($this->data_falecimento);
        }
        return null;
    }
}
