<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringOfDeliveries extends Model
{
    protected $table = 'monitoring_of_deliveries';
    protected $fillable = ['name', 'description'];
}
