<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DfeDistributionState extends Model
{
    protected $fillable = ['company_id', 'environment', 'last_nsu', 'max_nsu', 'next_query_at'];

    protected $casts = ['next_query_at' => 'datetime'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
