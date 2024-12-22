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
use Illuminate\Support\Facades\Log;
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
        $debug = request()->query('debug') ?? 'false';
        $customer_id = request()->query('customer_id');
        $location_id = auth()->user()->location_id;

        $filePath = public_path($this->exportLedger($from, $to, $customer_id, $location_id));


        return response()->download($filePath)->deleteFileAfterSend($debug != 'true');
    }
    public function exportLedger($from, $to, $customer_id, $location_id)
    {

        if (isset($customer_id) && ($customer_id != null) && ($customer_id != 'null')) {
            $filePath =  ($this->export($from, $to, $customer_id, $location_id));
        } else {
            $filePath =  ($this->exportAll($from, $to, $customer_id, $location_id));
        }

        return $filePath;
    }

    public function exportAll($from, $to, $customer_id, $location_id)
    {
        // Fetch the orders
        $query = Order::with('customer');

        if (isset($location_id)) {
            $query = $query->where('location_id', $location_id);
        }
        $dRange = 'All';

        if (isset($from) && isset($to)) {
            // Validate dates using strtotime
            $fromTimestamp = strtotime($from);
            $toTimestamp = strtotime($to);

            if ($fromTimestamp !== false && $toTimestamp !== false) {
                $toEndOfDay = date('Y-m-d 23:59:59', $toTimestamp); // Append end of day time
                $dRange = $from . ' to ' . $to;
                $query = $query->whereBetween('order_date_time', [$from, $toEndOfDay]);
            }
        } elseif (isset($from)) {
            // Validate date using strtotime
            $fromTimestamp = strtotime($from);

            if ($fromTimestamp !== false) {
                $dRange = $from;
                $query = $query->whereDate('order_date_time', '>=', $from);
            }
        } elseif (isset($to)) {
            // Validate date using strtotime
            $toTimestamp = strtotime($to);

            if ($toTimestamp !== false) {
                $toEndOfDay = date('Y-m-d 23:59:59', $toTimestamp); // Append end of day time
                $dRange = $to;
                $query = $query->where('order_date_time', '<=', $toEndOfDay);
            }
        }



        $orders = $query->get();
        $ordersArrayData = [];

        $avgFat = 0;
        $avgSNF = 0;
        $totalLitres = 0;
        $totalAmount = 0;
        $totalAdvance = 0;
        $totalPayment = 0;





        // If customer_id is null, group by customer_id and calculate sums

        if (isset($location_id)) {
            $orders = $orders->groupBy('customer_id');
        } else {
            $orders = $orders->groupBy('location_id');
        }


        foreach ($orders as  $group) {
            $total_litres = $group->sum(fn ($order) => $order->cow_litres + $order->buffalo_litres + $order->mixed_litres);
            $total_amount = $group->sum(fn ($order) => $order->cow_amt + $order->buffalo_amt + $order->mixed_amt);
            $total_advance = $group->sum('advance');
            $total_payment = $group->sum('payment');

            $totalAmount += $total_amount;
            $totalLitres += $total_litres;
            $totalAdvance += $total_advance;
            $totalPayment += $total_payment;
            $avg_fat = round($group->avg(fn ($order) => $order->cow_fat + $order->buffalo_fat + $order->mixed_fat), 2);
            $avg_snf = round($group->avg(fn ($order) => $order->cow_snf + $order->buffalo_snf + $order->mixed_snf), 2);
            $avgFat += $avg_fat;
            $avgSNF += $avg_snf;
            $closing_balance = $total_amount - $total_payment - $total_advance;



            $ordersArrayData[] = [
                'VSP' => $group->first()->location_id ?? 'N/A',
                'Member' => $group->first()->customer->last_name ?? 'N/A',
                'Date' => $dRange,
                'Shift' => 'Morning & Evening',
                'Type' => 'Cow & Buffalo',
                'Qty(ltr)' => $total_litres,
                'Avg Fat' => $avg_fat,
                'Avg SNF' => $avg_snf,

                'Rate' => round($total_amount / max(1, $total_litres), 2), // Average rate per litre
                'Amount' => $total_amount,

                'Advance' => $total_advance,
                'Payment' => $total_payment,

                'closing_balance' => $closing_balance,


                'balance' => $total_amount - $total_payment,

                'T_Amount' => $total_amount,




                'Remark' => '', // Placeholder for remarks
            ];
        }

        // Define headers
        $headers = [
            'VSP', 'Member', 'Date', 'Shift', 'Type', 'Qty(ltr)', 'Avg Fat',
            'Avg SNF',
            "LR",
            'Rate', 'Total Amount', "Balance", 'Advance',  "G. Total Amount",
            'Paid Amount', "Closing Balance", 'Remark',
        ];

        // Prepare data rows
        $orderData = [];
        foreach ($ordersArrayData as $order) {
            $orderData[] = [
                $order['VSP'],
                $order['Member'],
                $order['Date'],
                $order['Shift'],
                $order['Type'],
                $order['Qty(ltr)'],
                $order['Avg Fat'],
                $order['Avg SNF'],
                0,
                $order['Rate'],
                $order['Amount'],
                $order['balance'],

                $order['Advance'],
                $order['T_Amount'],
                $order['Payment'],
                $order['closing_balance'],
                $order['Remark'],
            ];
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

        $count = count($orderData);
        $count = max(1, $count);

        $sheet->setCellValue('E' . $row, 'Total');
        $sheet->mergeCells('A' . $row . ':E' . $row); // Merge for better display
        $sheet->setCellValue('F' . $row, $totalLitres); // Total Amount
        $sheet->setCellValue('G' . $row, round(($avgFat) / ($count), 2)); // Total Amount
        $sheet->setCellValue('H' . $row, round($avgSNF / $count, 2)); // Total Amount
        $sheet->setCellValue('K' . $row, $totalAmount); // Total Amount
        $sheet->setCellValue('M' . $row, $totalAdvance); // Total Advance
        $sheet->setCellValue('O' . $row, $totalPayment); // Total Payment
        // // Save the Excel file
        // $fileName = 'reports/ledger_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        // $writer = new Xlsx($spreadsheet);
        // $filePath = public_path($fileName);
        // if (!file_exists(public_path('reports'))) {
        //     mkdir(public_path('reports'));
        // }
        // $writer->save($filePath);

        // return $fileName;
        $fileName = 'reports/ledger_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $directoryPath = public_path('reports');
        $filePath = public_path($fileName);

        // Ensure the directory exists
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true); // Recursive directory creation
        }

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
        } catch (\Exception $e) {
            Log::error('Error saving Excel file: ' . $e->getMessage());
            throw $e; // Optional: rethrow for debugging
        }

        // Return the relative path for the file
        return $fileName;
    }


    public function  export($from, $to, $customer_id, $location_id)
    {
        // Fetch the orders
        $query = Order::with('customer');

        if (isset($location_id)) {
            $query = $query->where('location_id', $location_id);
        }
        if (isset($customer_id)) {
            $query = $query->where('customer_id', $customer_id);
        }
        $dRange = 'all';
        if (isset($from) && isset($to)) {
            // Validate dates using strtotime
            $fromTimestamp = strtotime($from);
            $toTimestamp = strtotime($to);

            if ($fromTimestamp !== false && $toTimestamp !== false) {
                $toEndOfDay = date('Y-m-d 23:59:59', $toTimestamp); // Append end of day time
                $dRange = $from . ' to ' . $to;
                $query = $query->whereBetween('order_date_time', [$from, $toEndOfDay]);
            }
        } elseif (isset($from)) {
            // Validate date using strtotime
            $fromTimestamp = strtotime($from);

            if ($fromTimestamp !== false) {
                $dRange = $from;
                $query = $query->whereDate('order_date_time', '>=', $from);
            }
        } elseif (isset($to)) {
            // Validate date using strtotime
            $toTimestamp = strtotime($to);

            if ($toTimestamp !== false) {
                $toEndOfDay = date('Y-m-d 23:59:59', $toTimestamp); // Append end of day time
                $dRange = $to;
                $query = $query->where('order_date_time', '<=', $toEndOfDay);
            }
        }


        $orders = $query->orderBy('order_date_time', 'ASC')->get();
        // Log::channel('callvcal')->info('export:'.json_encode($orders));

        // Define headers
        $headers = [
            'VSP', 'Member', 'Date', 'Shift', 'Type', 'Litres', 'Fat', 'CLR', 'SNF', 'Rate', 'Amount',  'Remark', 'Advance', 'Payment',
        ];

        // Initialize total counters
        $totalAmount = 0;
        $totalAdvance = 0;
        $totalPayment = 0;
        $count = 0;
        $avgFat = 0;
        $avgSNF = 0;
        $totalLitres = 0;


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

                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->cow_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
                $count++;
                $avgFat += $order->cow_fat;
                $avgSNF += $order->cow_snf;
                $totalLitres += $order->cow_litres;
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

                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->buffalo_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
                $count++;
                $avgFat += $order->buffalo_fat;
                $avgSNF += $order->buffalo_snf;
                $totalLitres += $order->buffalo_litres;
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

                    $order->remark,
                    $order->advance,
                    $order->payment,
                ];
                $totalAmount += $order->mixed_amt;
                $totalAdvance += $order->advance;
                $totalPayment += $order->payment;
                $count++;
                $avgFat += $order->mixed_fat;
                $avgSNF += $order->mixed_snf;
                $totalLitres += $order->mixed_litres;
            }
            if ($order->payment > 0) {
                $orderData[] = [
                    $order->location_id ?? 'N/A',
                    $order->customer->last_name ?? 'N/A',
                    $order->order_date_time,
                    '-',
                    '-',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',

                    $order->remark,
                    '',
                    $order->payment,
                ];

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

        $count = max(1, $count);
        // Add summation rows
        $sheet->setCellValue('A' . $row, 'Totals');
        $sheet->mergeCells('A' . $row . ':E' . $row); // Merge for better display
        $sheet->setCellValue('K' . $row, $totalAmount); // Total Amount
        $sheet->setCellValue('M' . $row, $totalAdvance); // Total Advance
        $sheet->setCellValue('N' . $row, $totalPayment); // Total Payment

        $sheet->setCellValue('F' . $row, $totalLitres); // Total Amount
        $sheet->setCellValue('G' . $row, round(($avgFat) / ($count), 2)); // Total Amount
        $sheet->setCellValue('I' . $row, round($avgSNF / $count, 2)); // Total Amount
        // Save the Excel file
        // $fileName = 'reports/ledger_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';


        // $writer = new Xlsx($spreadsheet);
        // $filePath = public_path($fileName);
        // if (!file_exists(public_path('reports'))) {
        //     mkdir(public_path('reports'));
        // }
        // $writer->save($filePath);

        // return $fileName;
        $fileName = 'reports/ledger_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        $directoryPath = public_path('reports');
        $filePath = public_path($fileName);

        // Ensure the directory exists
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true); // Recursive directory creation
        }

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
        } catch (\Exception $e) {
            Log::error('Error saving Excel file: ' . $e->getMessage());
            throw $e; // Optional: rethrow for debugging
        }

        // Return the relative path for the file
        return $fileName;
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
