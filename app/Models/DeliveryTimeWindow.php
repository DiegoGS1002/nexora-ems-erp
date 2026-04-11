<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryTimeWindow extends Model
{
    protected $table = 'delivery_time_windows';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'capacity',
        'region',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity'  => 'integer',
    ];

    public function deliverySchedules(): HasMany
    {
        return $this->hasMany(SchedulingOfDeliveries::class, 'time_window_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $time = substr($this->start_time, 0, 5) . ' às ' . substr($this->end_time, 0, 5);
        return $this->name . ' (' . $time . ')';
    }
}

