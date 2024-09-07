<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $table = 'userdata'; // Specify the table name

    protected $fillable = [
        'user_id',
        'route',
        'last_name',
        'add1',
        'contact',
        'amount',
        'crate',
        'type',
        'status',
        'location_id'
    ];

    protected $primaryKey = 'user_id';
    public $timestamps = false; // Assuming you don't need timestamps for this model
}
