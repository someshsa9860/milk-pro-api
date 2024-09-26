<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateList extends Model
{
    // Specify the table associated with the model
    protected $table = 'ratelist'; 

    // Define the primary key
    protected $primaryKey = 'srl';

    // If the primary key is auto-incrementing
    public $incrementing = true;

    // Disabling timestamps if not needed
    public $timestamps = false;

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'srl',    // Primary key
        'fat',    // Fat content (assumed)
        'snf',    // Solid-not-fat content (assumed)
        'rate',   // Rate value
        'location_id', // Foreign key for location
        'type',
        'shift'
    ];
}
