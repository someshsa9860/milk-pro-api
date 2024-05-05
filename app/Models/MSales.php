<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MSales extends Model
{
    protected $table = 'msales'; // Specify the table name

    protected $fillable = [
        'p_id',
        'indate',
        'billno',
        'bdate',
        'route',
        'cust_id',
        'cname',
        'cow',
        'buff',
        'total',
        'fat',
        'snf',
        'litres',
        'adv',
        'crin',
        'crout',
        'confirm',
        'usermail',
        'cprice',
        'bprice',
        'userrout',
    ];

    protected $primaryKey = 'p_id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
