<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SefazDiagnostic extends Model
{
    protected $fillable = [
        'rejection_code',
        'titulo',
        'causa',
        'solucao',
        'module',
        'severity',
        'related_codes',
        'tags',
        'active',
    ];

    protected $casts = [
        'related_codes' => 'array',
        'tags'          => 'array',
        'active'        => 'boolean',
    ];

    public static function findByCode(string $code): ?self
    {
        return static::where('rejection_code', $code)->where('active', true)->first();
    }
}

