<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class OrderItem extends Model
// {
//     protected $table = 'order_items';


//     protected $fillable = [
//         'order_id',
//         'user_id',
//         'customer_id',
//         'type',
//         'fat',
//         'snf',
//         'litres',
//         'clr',
//         'shift',
//         'amt',
//         'rate',
//     ];
// }



class OrderItem
{
    public $type;
    public $litres;
    public $fat;
    public $clr;
    public $snf;
    public $rate;
    public $amt;

    public function __construct($category, $litres, $fat, $clr, $snf, $rate, $amt)
    {
        $this->type = $category;
        $this->litres = $litres;
        $this->fat = $fat;
        $this->clr = $clr;
        $this->snf = $snf;
        $this->rate = $rate;
        $this->amt = $amt;
    }
}
