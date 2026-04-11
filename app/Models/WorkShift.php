<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// TimeRecord is in the same namespace (App\Models), no import needed.
class WorkShift extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'end_time',
        'break_duration',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'break_duration' => 'integer',
    ];

    public function timeRecords(): HasMany
    {
        return $this->hasMany(TimeRecord::class);
    }

    public function getWorkHoursAttribute(): string
    {
        return "{$this->start_time} - {$this->end_time}";
    }
}

