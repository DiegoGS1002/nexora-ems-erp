<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'ean',
        'description',
        'unit_of_measure',
        'sale_price',
        'stock',
        'expiration_date',
        'category',
        'image'
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'expiration_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            Supplier::class,
            'product_supplier'
        );
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/products/' . $this->image)
            : asset('images/no-image.png');
    }
}
