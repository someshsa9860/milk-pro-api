<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'mproducts'; // Specify the table name

    protected $fillable = [
        'p_id',
        'p_name',
        'price',
        'dprice',
        'rprice',
        'cprice',
        'tprice',
        'ml',
        'img',
    ];

    protected $primaryKey = 'p_id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
