<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vehicle extends Model
{
    protected $fillable = [
        'model',
        'brand',
        'plate',
        'renavam',
        'chassis',
        'fuel_type',
        'year',
        'color'
    ];
}
