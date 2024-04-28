<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'tblsupplier'; // Specify the table name

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'contact',
        'email',
        'address',
    ];

    protected $primaryKey = 'supplier_id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
