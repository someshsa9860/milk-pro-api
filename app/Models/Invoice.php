<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices'; // Specify the table name

    protected $fillable = [
        'id',
        'year1',
        'indate',
        'location_id'
    ];

    protected $primaryKey = 'id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
