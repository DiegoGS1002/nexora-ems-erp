<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsPayable extends Model
{
    protected $table = 'accounts_payable';
    protected $fillable = ['name', 'description', 'value', 'due_date'];
}
