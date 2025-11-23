<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'empresa_id',
        'tabela',
        'registro_id',
        'acao',
        'valores_antes',
        'valores_depois',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'valores_antes' => 'array',
        'valores_depois' => 'array',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
