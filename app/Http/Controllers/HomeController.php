<?php

namespace App\Http\Controllers;

use App\Models\AdsBanner;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\User;
use App\Models\WorkingLocation;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function fetch()
    {
        $location = (new SettingsController())->getLocation();

        return response(
            ['headerFooter' => (new SettingsController())->getHeaderFooter($location),
            
            'vTypes'=>(new VehicleController())->getTypes(),
            'rates'=>(new VehicleController())->getRates(),
            
            ]
        );
    }
}
