<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    use HasFactory;

    protected $table = 'permissoes';

    protected $fillable = [
        'user_id',
        'tabela',
        'consultar',
        'cadastrar',
        'editar',
        'excluir'
    ];

    protected $casts = [
        'consultar' => 'boolean',
        'cadastrar' => 'boolean',
        'editar' => 'boolean',
        'excluir' => 'boolean',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function podeConsultar()
    {
        return $this->consultar;
    }

    public function podeCadastrar()
    {
        return $this->cadastrar;
    }

    public function podeEditar()
    {
        return $this->editar;
    }

    public function podeExcluir()
    {
        return $this->excluir;
    }
}
