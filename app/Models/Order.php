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
        'cow_litres',
        'cow_fat',
        'cow_clr',
        'cow_snf',
        'cow_rate',
        'cow_amt',
        'buffalo_litres',
        'buffalo_fat',
        'buffalo_clr',
        'buffalo_snf',
        'buffalo_rate',
        'buffalo_amt',
        'mixed_litres',
        'mixed_fat',
        'mixed_clr',
        'mixed_snf',
        'mixed_rate',
        'mixed_amt',
    ];
    
    
    public function items()
    {
        return $this->hasMany(OrderItem::class,'order_id');
    }
    public function getTotal()
    {
        return $this->total;
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
