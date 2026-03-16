<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaccaratAccount extends Model
{
    protected $table = 'baccarat_accounts';
    protected $fillable = ['name', 'description'];
}
