<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalNumberSequence extends Model
{
    protected $fillable = [
        'company_id',
        'document_type',
        'series',
        'environment',
        'next_number',
    ];

    protected function casts(): array
    {
        return ['next_number' => 'integer'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
