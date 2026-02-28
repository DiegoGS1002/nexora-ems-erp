<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'identification_number',
        'role',
        'email',
        'phone_number',
        'address',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
