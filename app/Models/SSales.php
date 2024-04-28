<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SSales extends Model
{
    protected $table = 'ssales'; // Specify the table name

    protected $fillable = [
        'id',
        'p_id',
        'billno',
        'cust_id',
        'p_name',
        'price',
        'qty',
    ];

    protected $primaryKey = 'id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
