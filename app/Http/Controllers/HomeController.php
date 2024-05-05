<?php

namespace App\Http\Controllers;

use App\Models\AdsBanner;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\MSales;
use App\Models\User;
use App\Models\UserData;
use App\Models\WorkingLocation;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function fetch()
    {

        return response(
            [
                'retailers'=>UserData::all(),
                'staffs'=>User::all(),
                'orders'=>MSales::all(),
                
            
            ]
        );
    }
}
