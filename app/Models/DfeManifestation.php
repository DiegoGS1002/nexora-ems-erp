<?php

namespace App\Models;

use App\Enums\RecipientManifestationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfeManifestation extends Model
{
    protected $fillable = [
        'dfe_document_id', 'type', 'sequence', 'justification', 'status',
        'protocol', 'sefaz_code', 'sefaz_message', 'response_xml', 'registered_at',
    ];

    protected $casts = [
        'type' => RecipientManifestationType::class,
        'registered_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(DfeDocument::class, 'dfe_document_id');
    }
}
