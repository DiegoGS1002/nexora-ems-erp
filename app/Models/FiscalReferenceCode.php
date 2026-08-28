<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalReferenceCode extends Model
{
    protected $fillable = ['type', 'code', 'description', 'metadata', 'valid_from', 'valid_until'];

    protected $casts = ['metadata' => 'array', 'valid_from' => 'date', 'valid_until' => 'date'];
}
