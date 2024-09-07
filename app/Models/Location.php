<?php

namespace App\Models;

use App\Admin\Forms\RateChart;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected static function booted()
    {
        static::created(function ($model) {
            $location_id=$model->location_id;
            (new RateChart())->check($location_id);
        });
        static::deleting(function ($model) {
            $location_id=$model->location_id;
            RateList::where('location_id',$location_id)->delete();
        });
    }
}
