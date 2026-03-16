<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExitRecord extends Model
{
    protected $table = 'exits';
    protected $fillable = ['name', 'description'];
}
