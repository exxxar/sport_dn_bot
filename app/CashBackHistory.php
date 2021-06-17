<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashBackHistory extends Model
{
    //
    protected $fillable = [
        'amount',
        'bill_number',
        'money_in_bill',
        'employee_id',
        'user_id',
        'type',
    ];
}
