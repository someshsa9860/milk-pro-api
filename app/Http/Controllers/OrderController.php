<?php

namespace App\Http\Controllers;

use App\Admin\Forms\NewOrder;
use App\Models\Invoice;
use App\Models\MSales;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenAdmin\Admin\Layout\Content;

class OrderController extends Controller
{
    public function newOrder(Content $content)
    {
        return $content
            ->title('New Order')
            ->body(new NewOrder());
    }
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

        $order = [
            'user_id' => auth()->user()->id,
            'customer_id' => $customer_id,
            'order_date_time' => $request->order_date_time ?? date("Y-m-d h:i:s"),
            'shift' => $shift,
            'remark' => $request->remark,

            'bill_no' => $request->bill_no ?? $this->generateInvoice(),
        ];
        $order = $this->makeOrderItem($order, $cow, 'cow_');
        $order = $this->makeOrderItem($order, $buffalo, 'buffalo_');
        $order = $this->makeOrderItem($order, $mixed, 'mixed_');
        $order = Order::updateOrCreate(
            [
                'id' => $request->id,
                'location_id' => auth()->user()->location_id
            ],
            $order

        );


        $order->load(['customer']);

        $total = 0;
        $total = $total + $order->cow_amt ?? 0;
        $total = $total + $order->buffalo_amt ?? 0;
        $total = $total + $order->mixed_amt ?? 0;
        $order->total = $total;
        $order->save();



        return response($order);
    }

    public function orders()
    {
        return response(Order::with(['customer'])->where('location_id', auth()->user()->location_id)->get());
    }

    public function makeOrderItem(array $order, $itemData, $type)
    {
        // if ($itemData != null) {
        //     OrderItem::updateOrCreate(
        //         ['id' => $itemData['id']??null],
        //         [
        //             'order_id' => $order->id,
        //             'user_id' => $order->user_id,
        //             'fat' => $order->fat,
        //             'customer_id' => $order->customer_id,
        //             'type' => $type,
        //             'snf' => $itemData['snf'],
        //             'clr' => $itemData['clr'],
        //             'fat' => $itemData['fat'],
        //             'litres' => $itemData['litres'],
        //             'amt' => $itemData['amt'],
        //             'rate' => $itemData['rate'],
        //         ]
        //     );
        // }
        if ($itemData != null) {
            $order[$type . 'fat'] = $itemData['fat'] ?? 0;
            $order[$type . 'snf'] = $itemData['snf'] ?? 0;
            $order[$type . 'clr'] = $itemData['clr'] ?? 0;
            $order[$type . 'litres'] = $itemData['litres'] ?? 0;
            $order[$type . 'amt'] = $itemData['amt'] ?? 0;
            $order[$type . 'rate'] = $itemData['rate'] ?? 0;
        }

        return $order;
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

        return response([
            'message' => "Deleted successfully"
        ]);
    }
}
