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
        $items = [];

        if ($this->cow_litres || $this->cow_fat || $this->cow_clr || $this->cow_snf || $this->cow_rate || $this->cow_amt) {
            $items[] = new OrderItem('cow', $this->cow_litres, $this->cow_fat, $this->cow_clr, $this->cow_snf, $this->cow_rate, $this->cow_amt);
        }

        if ($this->buffalo_litres || $this->buffalo_fat || $this->buffalo_clr || $this->buffalo_snf || $this->buffalo_rate || $this->buffalo_amt) {
            $items[] = new OrderItem('buffalo', $this->buffalo_litres, $this->buffalo_fat, $this->buffalo_clr, $this->buffalo_snf, $this->buffalo_rate, $this->buffalo_amt);
        }

        if ($this->mixed_litres || $this->mixed_fat || $this->mixed_clr || $this->mixed_snf || $this->mixed_rate || $this->mixed_amt) {
            $items[] = new OrderItem('mixed', $this->mixed_litres, $this->mixed_fat, $this->mixed_clr, $this->mixed_snf, $this->mixed_rate, $this->mixed_amt);
        }

        return $items;
    }




    public function customer()
    {
        return $this->belongsTo(UserData::class, 'customer_id');
    }






   
    
}
