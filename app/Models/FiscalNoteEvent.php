<?php

namespace App\Models;

use App\Enums\FiscalNoteEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiscalNoteEvent extends Model
{
    protected $fillable = [
        'fiscal_note_id',
        'event_type',
        'description',
        'protocol',
        'response_data',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => FiscalNoteEventType::class,
            'response_data' => 'array',
        ];
    }

    public function fiscalNote(): BelongsTo
    {
        return $this->belongsTo(FiscalNote::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
