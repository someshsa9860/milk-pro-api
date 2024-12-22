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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function customers(Request $request)
    {
        $location_id = $request->get('query');
        $data= UserData::where('location_id', $location_id)->get(DB::raw('user_id as id, last_name as text'));
        // Log::channel('callvcal')->info(json_encode($request->all()).' location_id'.$location_id.' data:'.json_encode($data));
        // return Province::city()->where('province_id', $provinceId)->get(['id', DB::raw('name as text')]);
        return response()->json($data);
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
