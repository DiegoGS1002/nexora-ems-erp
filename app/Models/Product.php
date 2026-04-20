<?php

namespace App\Models;

use App\Enums\TipoProduto;
use App\Enums\NaturezaProduto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'product_code',
        'name',
        'ean',
        'ncm',
        'cfop_saida',
        'cfop_entrada',
        'grupo_tributario_id',
        'unit_of_measure_id',
        'product_category_id',
        'description',
        'short_description',
        'brand',
        'product_type',
        'nature',
        'product_line',
        'unit_of_measure',
        'category',
        'sale_price',
        'cost_price',
        'stock',
        'stock_min',
        'expiration_date',
        'weight_net',
        'weight_gross',
        'height',
        'width',
        'depth',
        'full_description',
        'is_active',
        'highlights',
        'tags',
        'image',
    ];

    protected $casts = [
        'sale_price'   => 'decimal:2',
        'cost_price'   => 'decimal:2',
        'weight_net'   => 'decimal:3',
        'weight_gross' => 'decimal:3',
        'height'       => 'decimal:2',
        'width'        => 'decimal:2',
        'depth'        => 'decimal:2',
        'expiration_date' => 'date',
        'is_active'    => 'boolean',
        'highlights'   => 'array',
        'tags'         => 'array',
        'product_type' => TipoProduto::class,
        'nature'       => NaturezaProduto::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->id = Str::uuid();

            if (empty($model->product_code)) {
                $last = static::withTrashed()->count();
                $model->product_code = 'PROD-' . str_pad($last + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    public function grupoTributario(): BelongsTo
    {
        return $this->belongsTo(GrupoTributario::class, 'grupo_tributario_id');
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_of_measure_id');
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }
}
