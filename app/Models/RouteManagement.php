<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteManagement extends Model
{
    protected $table = 'route_managements';

    protected $fillable = ['name', 'description'];
}
