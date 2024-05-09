<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\MSales;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public  function createOrder(Request $request)
    {
        $request->validate([
            'cust_id'=>'required',
            'shift'=>'required',
        ]);
        $customer=UserData::find($request->cust_id);
        $sale=MSales::updateOrCreate([
            'p_id'=>$request->id
        ],[
            'usermail'=>auth()->user()->email,
            'userrout'=>auth()->user()->route,
            'indate'=>now(tz:'Asia/Kolkata'),
            'route'=>$customer->route,
            'cname'=>$customer->last_name,
            'cname'=>$customer->last_name,
            'billno'=>$this->generateInvoice(),
            'shift'=>$request->shift,
            'shift'=>$request->shift,



        ]);
    }
    public function generateInvoice()
    {
        // Get the current date
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        $currentMonth = $currentDate->month;
        
        // Check if the current month and day are before March 31st
        if ($currentMonth < 3 || ($currentMonth === 3 && $currentDate->day < 31)) {
            $yearToShow = $currentYear - 1;
        } else {
            $yearToShow = $currentYear;
        }
        // Check if a token entry exists for today's date
        $existing = Invoice::create([
            'year1'=>$currentYear,
            'indate'=>$currentDate->format('Y-m-d'),
        ]);

        return $existing->id;
    }
}
