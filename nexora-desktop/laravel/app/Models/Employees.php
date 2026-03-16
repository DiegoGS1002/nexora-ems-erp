<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employees extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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
