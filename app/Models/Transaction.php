<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'txn_id',
        'user_email',
        'amount',
        'currency_sign',
        'currency_value'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
