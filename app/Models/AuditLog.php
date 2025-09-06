<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'user_id',
        'target_type',
        'target_id',
        'evento',
        'diff',
        'meta',
        'correlation_id',
        'tz_offset',
    ];

    protected $casts = [
        'diff' => 'array',
        'meta' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
