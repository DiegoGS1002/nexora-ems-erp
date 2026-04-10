<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class UnitOfMeasure extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'units_of_measure';

    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->id = Str::uuid();

            $model->abbreviation = strtoupper(trim($model->abbreviation));
        });

        static::updating(function (self $model) {
            if ($model->isDirty('abbreviation')) {
                $model->abbreviation = strtoupper(trim($model->abbreviation));
            }
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unit_of_measure_id');
    }

    public function getLabelAttribute(): string
    {
        return "{$this->abbreviation} — {$this->name}";
    }
}


