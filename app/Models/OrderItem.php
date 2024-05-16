<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';


    protected $fillable = [
        'order_id',
        'user_id',
        'customer_id',
        'type',
        'fat',
        'snf',
        'litres',
        'clr',
        'shift',
        'amt',
        'rate',
    ];
}
