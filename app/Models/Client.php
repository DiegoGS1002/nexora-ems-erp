<?php

namespace App\Models;

use App\Enums\TipoPessoa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tipo_pessoa',
        'name',
        'social_name',
        'taxNumber',
        'email',
        'phone_number',
        'address',
        'address_zip_code',
        'address_street',
        'address_number',
        'address_complement',
        'address_district',
        'address_city',
        'address_state',
    ];

    protected $casts = [
        'tipo_pessoa' => TipoPessoa::class,
    ];

    public function getFullAddress(): string
    {
        return trim(implode(', ', array_filter([
            $this->address_street,
            $this->address_number,
            $this->address_complement,
            $this->address_district,
            $this->address_city,
            $this->address_state,
            $this->address_zip_code,
        ])));
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
