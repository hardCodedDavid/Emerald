<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank_name', 'account_name', 'account_number', 'user', 'account_information'
    ];
    
}
