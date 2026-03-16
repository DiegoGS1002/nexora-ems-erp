<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    protected $table = 'accounts_payable';
    protected $fillable = ['name', 'description'];
}
