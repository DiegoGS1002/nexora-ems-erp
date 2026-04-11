<?php

namespace App\Models;

use App\Enums\DeliveryPriority;
use App\Enums\DeliveryScheduleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchedulingOfDeliveries extends Model
{
    protected $table = 'scheduling_of_deliveries';

    protected $fillable = [
        'schedule_number',
        'order_id',
        'client_name',
        'delivery_address',
        'delivery_date',
        'time_window_id',
        'vehicle_id',
        'driver_id',
        'driver_name',
        'weight_kg',
        'volume_m3',
        'priority',
        'status',
        'notes',
        'receiver_name',
        'receiver_document',
        'delivered_at',
        'rescheduled_from_id',
        'reschedule_reason',
    ];

    protected $casts = [
        'status'       => DeliveryScheduleStatus::class,
        'priority'     => DeliveryPriority::class,
        'delivery_date'=> 'date',
        'delivered_at' => 'datetime',
        'weight_kg'    => 'decimal:3',
        'volume_m3'    => 'decimal:3',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->schedule_number)) {
                $model->schedule_number = 'AG-' . str_pad(
                    (static::max('id') ?? 0) + 1,
                    5, '0', STR_PAD_LEFT
                );
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function timeWindow(): BelongsTo
    {
        return $this->belongsTo(DeliveryTimeWindow::class, 'time_window_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rescheduled_from_id');
    }
}
