<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

        'order_date_time',
        'bill_no',
        'shift',
        'total',
        'advance',
        'customer_id',
        'user_id',

    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id');
    }


    public function customer()
    {
        return $this->belongsTo(UserData::class,'customer_id');
    }

    


    

    public function attachItem($item)
    {
        $this->items()->attach($item);
    }

    public function detachItem($item)
    {
        $this->items()->detach($item);
    }

}
