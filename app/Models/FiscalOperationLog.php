<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalOperationLog extends Model
{
    protected $fillable = ['company_id', 'fiscal_note_id', 'operation', 'payload', 'result', 'user_id', 'occurred_at'];

    protected $casts = ['payload' => 'array', 'result' => 'array', 'occurred_at' => 'immutable_datetime'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('O log fiscal é imutável.'));
        static::deleting(fn () => throw new \LogicException('O log fiscal é imutável.'));
    }
}
