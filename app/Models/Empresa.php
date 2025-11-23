<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'cnpj',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function sepultamentos()
    {
        return $this->hasMany(Sepultamento::class);
    }

    // Mutators
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

        public static function generateUniqueSlug($nome)
    {
        $slug = Str::slug($nome);
        $original = $slug;
        $count = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$count;
            ++$count;
        }

        return $slug;
    }
}
