<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminDeviceList extends Model
{
    protected $table = 'admin_device_lists';

    protected $fillable = [

        'full_device_name',
        'admin_id',
        'block',
        'ip_addresses',
        'device_id',
        'status',
        'last_accessed',
        'last_logout_at',
        'last_login_at',
        'uuid',
        'device_name',
        'device_model','device_ad_id'


    ];
}
