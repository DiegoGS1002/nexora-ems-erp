<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulingOfDeliveries extends Model
{
    protected $table = 'scheduling_of_deliveries';
    protected $fillable = ['name', 'description'];
}
