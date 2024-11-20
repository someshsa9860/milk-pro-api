<?php

namespace App\Http\Controllers;

use App\Admin\Forms\NewOrder;
use App\Models\Invoice;
use App\Models\MSales;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $order_date_time = $request->order_date_time;
        if (isset($request->order_date_time)) {
            $user = User::find(auth()->user()->id);
            if ($user->can_edit_order_date != 1) {
                $order_date_time = null;
            }
        }

        if ($request->is_sell == 1) {
            $order = Order::updateOrCreate(
                [
                    'id' => $request->id,
                    'location_id' => auth()->user()->location_id ?? Admin::user()->id
                ],
                [
                    'customer_id' => $request->customer_id,
                    'is_sell' => $request->is_sell,
                    'payment' => $request->payment,
                    'order_date_time' => $order_date_time ?? date("Y-m-d h:i:s"),
                    'remark' => $request->remark,
                ]
            );
            $order->load(['customer']);
            return response($order);
        }

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
            'advance' => $request->advance,
            'bill_no' => $request->bill_no ?? $this->generateInvoice(),
        ];
        $order = $this->makeOrderItem($order, $cow, 'cow_');
        $order = $this->makeOrderItem($order, $buffalo, 'buffalo_');
        $order = $this->makeOrderItem($order, $mixed, 'mixed_');
        $order = Order::updateOrCreate(
            [
                'id' => $request->id,
                'location_id' => auth()->user()->location_id ?? Admin::user()->id
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





    public function printLedger()
    {
        $from = request()->query('from');
        $to = request()->query('to');
        $customer_id = request()->query('customer_id');
        $location_id = auth()->user()->location_id;

        $filePath= public_path($this->export($from,$to,$customer_id,$location_id)) ;
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function  export($from,$to,$customer_id,$location_id){
        // Fetch the orders
        $query = Order::with('customer')->where('location_id',$location_id);
        if (isset($customer_id)) {
            $query = $query->where('customer_id', $customer_id);
        }
        if (isset($from)) {
            $query = $query->where('order_date_time', '>=', $from);
        }
        if (isset($to)) {
            $query = $query->where('order_date_time', '<=', $to);
        }
        $orders = $query->get();

        // Define headers
        $headers = [
            'VSP', 'Member', 'Date', 'Shift', 'Type', 'Litres', 'Fat', 'CLR', 'SNF', 'Rate', 'Amount', 'Total', 'Remark', 'Advance', 'Payment',
        ];

        // Initialize total counters
        $totalAmount = 0;
        $totalAdvance = 0;
        $totalPayment = 0;

        // Prepare data rows
        $orderData = [];
        foreach ($orders as $order) {
            // Add cow details if amount > 0
            if ($order->cow_amt > 0) {
                $orderData[] = [
                    $order->location_id ?? 'N/A',
                    $order->customer->last_name ?? 'N/A',
                    $order->order_date_time,
                    $order->shift,
                    'Cow',
                    $order->cow_litres,
                    $order->cow_fat,
                    $order->cow_clr,
                    $order->cow_snf,
                    $order->cow_rate,
                    $order->cow_amt,
                    $order->total,
                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->cow_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
            }

            // Add buffalo details if amount > 0
            if ($order->buffalo_amt > 0) {
                $orderData[] = [
                    $order->location_id ?? 'N/A',
                    $order->customer->last_name ?? 'N/A',
                    $order->order_date_time,
                    $order->shift,
                    'Buffalo',
                    $order->buffalo_litres,
                    $order->buffalo_fat,
                    $order->buffalo_clr,
                    $order->buffalo_snf,
                    $order->buffalo_rate,
                    $order->buffalo_amt,
                    $order->total,
                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->buffalo_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
            }

            // Add mixed details if amount > 0
            if ($order->mixed_amt > 0) {
                $orderData[] = [
                    $order->location_id ?? 'N/A',
                    $order->customer->last_name ?? 'N/A',
                    $order->order_date_time,
                    $order->shift,
                    'Mixed',
                    $order->mixed_litres,
                    $order->mixed_fat,
                    $order->mixed_clr,
                    $order->mixed_snf,
                    $order->mixed_rate,
                    $order->mixed_amt,
                    $order->total,
                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->mixed_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
            }
        }

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers to the first row
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '1', $header);
            $columnIndex++;
        }

        // Add order data to the spreadsheet
        $row = 2; // Start from the second row
        foreach ($orderData as $data) {
            $columnIndex = 'A'; // Reset column index for each row
            foreach ($data as $value) {
                $sheet->setCellValue($columnIndex . $row, $value);
                $columnIndex++;
            }
            $row++;
        }

        // Add summation rows
        $sheet->setCellValue('A' . $row, 'Totals');
        $sheet->mergeCells('A' . $row . ':E' . $row); // Merge for better display
        $sheet->setCellValue('K' . $row, $totalAmount); // Total Amount
        $sheet->setCellValue('M' . $row, $totalAdvance); // Total Advance
        $sheet->setCellValue('N' . $row, $totalPayment); // Total Payment

        // Save the Excel file
        $fileName = 'ledger_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $filePath = public_path($fileName);
        $writer->save($filePath);

        return $fileName;
        // Return the file for download
        return response()->download($fileName)->deleteFileAfterSend(true);
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
