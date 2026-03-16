<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    protected $table = 'accounts_receivable';
    protected $fillable = ['name', 'description'];
}
