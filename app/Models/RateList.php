<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateList extends Model
{
    protected $table = 'ratelist'; // Specify the table name

    protected $fillable = [
        'fat',
        'snf1',
        'snf2',
        'snf3',
        'snf4',
        'snf5',
        'snf6',
        'snf7',
        'snf8',
        'snf9',
        'snf10',
        'snf11',
        'snf12',
        'snf13',
    ];

    // Assuming there is no primary key defined explicitly in the table.
    public $incrementing = false; // Set to false if primary key is not auto-incrementing
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
