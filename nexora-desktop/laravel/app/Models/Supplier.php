<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'social_name',
        'taxNumber',
        'email',
        'address_zip_code',
        'address_street',
        'address_number',
        'address_complement',
        'address_district',
        'address_city',
        'address_state',
        'phone_number',
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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_supplier'
        );
    }

}
