<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CausaMorte extends Model
{
    use HasFactory;

    protected $table = 'causas_morte';

    protected $fillable = [
        'descricao',
        'codigo_cid10',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Relações
    public function sepultamentos()
    {
        return $this->belongsToMany(Sepultamento::class, 'sepultamento_causa', 'causa_morte_id', 'sepultamento_id')
                    ->withTimestamps();
    }
}
