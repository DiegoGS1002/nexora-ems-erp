<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhReport extends Model
{
    protected $table = 'rh_reports';
    protected $fillable = ['name', 'description'];
}
