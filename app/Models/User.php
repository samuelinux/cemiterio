<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'tipo_usuario',
        'ativo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function permissoes()
    {
        return $this->hasMany(Permissao::class);
    }

    public function sepultamentos()
    {
        return $this->hasMany(Sepultamento::class);
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeAdmin($query)
    {
        return $query->where('tipo_usuario', 'admin');
    }

    public function scopeUser($query)
    {
        return $query->where('tipo_usuario', 'user');
    }

    // Helpers
    public function isAdmin()
    {
        return $this->tipo_usuario === 'admin';
    }

    public function isUser()
    {
        return $this->tipo_usuario === 'user';
    }
}
