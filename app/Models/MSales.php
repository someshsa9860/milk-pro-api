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
        'm1',
        'm2',
        'm3',
        'm4',
        'm5',
        'm6',
        'm7',
        'total',
        'litres',
        'adv',
        'crin',
        'crout',
        'confirm',
        'usermail',
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'userrout',
    ];

    protected $primaryKey = 'p_id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
