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
        'remark',
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



    // protected function formatDecimal($value)
    // {
    //     return $value;
    //     return is_numeric($value) ? (float)$value : $value;
    // }

    // public function setCowFatAttribute($value)
    // {
    //     $this->attributes['cow_fat'] = $this->formatDecimal($value);
    // }

    // public function setCowSnfAttribute($value)
    // {
    //     $this->attributes['cow_snf'] = $this->formatDecimal($value);
    // }

    // public function setCowLitresAttribute($value)
    // {
    //     $this->attributes['cow_litres'] = $this->formatDecimal($value);
    // }

    // public function setCowAmtAttribute($value)
    // {
    //     $this->attributes['cow_amt'] = $this->formatDecimal($value);
    // }

    // public function setCowRateAttribute($value)
    // {
    //     $this->attributes['cow_rate'] = $this->formatDecimal($value);
    // }

    // public function setCowClrAttribute($value)
    // {
    //     $this->attributes['cow_clr'] = $this->formatDecimal($value);
    // }

    // public function setMixedFatAttribute($value)
    // {
    //     $this->attributes['mixed_fat'] = $this->formatDecimal($value);
    // }

    // public function setMixedSnfAttribute($value)
    // {
    //     $this->attributes['mixed_snf'] = $this->formatDecimal($value);
    // }

    // public function setMixedLitresAttribute($value)
    // {
    //     $this->attributes['mixed_litres'] = $this->formatDecimal($value);
    // }

    // public function setMixedAmtAttribute($value)
    // {
    //     $this->attributes['mixed_amt'] = $this->formatDecimal($value);
    // }

    // public function setMixedRateAttribute($value)
    // {
    //     $this->attributes['mixed_rate'] = $this->formatDecimal($value);
    // }

    // public function setMixedClrAttribute($value)
    // {
    //     $this->attributes['mixed_clr'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloFatAttribute($value)
    // {
    //     $this->attributes['buffalo_fat'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloSnfAttribute($value)
    // {
    //     $this->attributes['buffalo_snf'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloLitresAttribute($value)
    // {
    //     $this->attributes['buffalo_litres'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloAmtAttribute($value)
    // {
    //     $this->attributes['buffalo_amt'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloRateAttribute($value)
    // {
    //     $this->attributes['buffalo_rate'] = $this->formatDecimal($value);
    // }

    // public function setBuffaloClrAttribute($value)
    // {
    //     $this->attributes['buffalo_clr'] = $this->formatDecimal($value);
    // }


   
    
}
