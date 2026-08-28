<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfeDocument extends Model
{
    protected $fillable = [
        'company_id', 'environment', 'nsu', 'access_key', 'schema', 'document_type',
        'status', 'xml_path', 'raw_xml', 'processed_at',
    ];

    protected $casts = ['processed_at' => 'datetime'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
