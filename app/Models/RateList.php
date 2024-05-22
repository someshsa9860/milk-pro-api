<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateList extends Model
{
    protected $table = 'ratelist'; // Specify the table name

   
    

    // Assuming there is no primary key defined explicitly in the table.
    public $incrementing = false; // Set to false if primary key is not auto-incrementing
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
