<?php

namespace App\Models;

use App\Enums\TimeRecordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'work_shift_id',
        'date',
        'clock_in',
        'break_start',
        'break_end',
        'clock_out',
        'status',
        'ip_address',
        'latitude',
        'longitude',
        'observation',
    ];

    protected $casts = [
        'date'        => 'date',
        'clock_in'    => 'datetime',
        'break_start' => 'datetime',
        'break_end'   => 'datetime',
        'clock_out'   => 'datetime',
        'status'      => TimeRecordStatus::class,
        'latitude'    => 'decimal:8',
        'longitude'   => 'decimal:8',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function getWorkedMinutesAttribute(): int
    {
        if (! $this->clock_in || ! $this->clock_out) {
            return 0;
        }

        $total = $this->clock_in->diffInMinutes($this->clock_out);
        $break = 0;

        if ($this->break_start && $this->break_end) {
            $break = $this->break_start->diffInMinutes($this->break_end);
        }

        return max(0, $total - $break);
    }

    public function getWorkedHoursFormattedAttribute(): string
    {
        $minutes = $this->worked_minutes;
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return sprintf('%02dh%02d', $h, $m);
    }
}

