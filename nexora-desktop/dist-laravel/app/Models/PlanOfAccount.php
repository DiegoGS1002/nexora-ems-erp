<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanOfAccount extends Model
{
    protected $table = 'plans_of_accounts';
    protected $fillable = ['name', 'description'];
}
