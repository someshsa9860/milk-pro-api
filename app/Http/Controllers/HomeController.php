<?php

namespace App\Http\Controllers;

use App\Admin\Forms\RateChart;
use App\Models\AdsBanner;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\MSales;
use App\Models\Order;
use App\Models\RateList;
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
                'retailers' => UserData::where('location_id', auth()->user()->location_id)->get(),
                'staffs' => User::where('location_id', auth()->user()->location_id)->get(),
                'orders' => Order::with('customer')->where('location_id', auth()->user()->location_id)->get(),
                'rates' => RateList::where('location_id', auth()->user()->location_id)->get()
            ]
        );
    }
    public function getRates()
    {

        return response(
            [
                'rates' => RateList::where('location_id', auth()->user()->location_id)->get()
            ]
        );
    }
}
