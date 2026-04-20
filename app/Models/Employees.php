<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employees extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name',
        'social_name',
        'identification_number',
        'rg',
        'rg_issuer',
        'rg_date',
        'birth_date',
        'gender',
        'marital_status',
        'nationality',
        'birthplace',
        'role',
        'email',
        'phone_number',
        'phone_secondary',
        'address',
        'zip_code',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'country',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'photo',
        'access_profile',
        'is_active',
        'admission_date',
        'work_schedule',
        'allow_system_access',
        'department',
        'salary',
        'internal_code',
        'observations',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'allow_system_access'  => 'boolean',
        'birth_date'           => 'date',
        'rg_date'              => 'date',
        'admission_date'       => 'date',
        'salary'               => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
