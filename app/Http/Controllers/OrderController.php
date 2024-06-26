<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\MSales;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public  function place(Request $request)
    {
        // $request->validate([
        //     'cust_id'=>'required',
        //     'shift'=>'required',
        // ]);
        // $customer=UserData::find($request->cust_id);
        // $sale=MSales::updateOrCreate([
        //     'p_id'=>$request->id
        // ],[
        //     'usermail'=>auth()->user()->email,
        //     'userrout'=>auth()->user()->route,
        //     'indate'=>now(tz:'Asia/Kolkata'),
        //     'route'=>$customer->route,
        //     'cname'=>$customer->last_name,
        //     'cname'=>$customer->last_name,
        //     'billno'=>$this->generateInvoice(),
        //     'shift'=>$request->shift,
        // ]);

        $request->validate([
            'customer_id' => ['required'],
            'shift' => ['required'],
        ]);

        $cow = $request->cow;
        $customer_id = $request->customer_id;
        $shift = $request->shift;
        $buffalo = $request->buffalo;
        $mixed = $request->mixed;

        $order = Order::updateOrCreate(
            [
                'id' => $request->id,
            ],
            [
                'user_id' => auth()->user()->id,
                'customer_id' => $customer_id,
                'order_date_time' => $request->order_date_time ?? now(),
                'shift' => $shift,
                'bill_no' => $request->bill_no ?? $this->generateInvoice(),
            ]

        );

        $this->makeOrderItem($order, $cow, 'cow');
        $this->makeOrderItem($order, $buffalo, 'buffalo');
        $this->makeOrderItem($order, $mixed, 'mixed');
        $order->load(['items', 'customer']);

        $total=0;
        foreach ($order->items as $item) {
            $total=$total+$item->amt;
        }
        $order->total=$total;
        $order->save();



        return response($order);
    }

    public function orders()
    {
        return response(Order::with(['items', 'customer'])->get());
    }

    public function makeOrderItem(Order $order, $itemData, $type)
    {
        if ($itemData != null) {
            OrderItem::updateOrCreate(
                ['id' => $itemData['id']??null],
                [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'fat' => $order->fat,
                    'customer_id' => $order->customer_id,
                    'type' => $type,
                    'snf' => $itemData['snf'],
                    'clr' => $itemData['clr'],
                    'fat' => $itemData['fat'],
                    'litres' => $itemData['litres'],
                    'amt' => $itemData['amt'],
                    'rate' => $itemData['rate'],
                ]
            );
        }
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
            'year1' => $currentYear,
            'indate' => $currentDate->format('Y-m-d'),
        ]);

        return $existing->id;
    }
    function delete($id)
    {
        Order::where('id', $id)->delete();
        OrderItem::where('order_id', $id)->delete();

        return response([
            'message' => "Deleted successfully"
        ]);
    }
}
