<?php

namespace App\Admin\Forms;

use App\Admin\Controllers\OrderController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use App\Jobs\RateImporter;
use App\Models\Location;
use App\Models\RateList;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use OpenAdmin\Admin\Widgets\Form;
use Symfony\Component\Translation\Loader\CsvFileLoader;

class DownloadReport extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Export Reports';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        // Generate the file path
        $filePath = (new ControllersOrderController())->exportLedger($request->from, $request->to, $request->customer_id, $request->location_id);
    
        // Ensure the file path is URL-compatible
        $fileName = str_replace(public_path(), '', $filePath);
        $downloadUrl = url($fileName);
    
        // Log for debugging
        Log::info("Generated File Path: $filePath");
        Log::info("Relative File Name: $fileName");
        Log::info("Download URL: $downloadUrl");
    
        // Return HTML with JavaScript redirection
        return response()->make(
            "<html>
                <head>
                    <script>
                        // Redirect to the download URL
                        window.location.href = '$downloadUrl';
                        
                        // After a short delay, go back to the previous page
                        setTimeout(function() {
                            window.history.back();
                        }, 2000);
                    </script>
                </head>
                <body>
                    <p>Redirecting to download...</p>
                </body>
            </html>"
        );
    }
    







    /**
     * Build a form here.
     */
    public function form()
    {



        $this->date('from', 'From Date');
        $this->date('to', 'To Date');
        $this->select('location_id', 'VSP')->options(Location::pluck('location_id', 'location_id'))->load('customer_id', '/admin/api/customers');;
        // $this->select('customer_id', 'Farmer')->options(UserData::pluck('last_name', 'user_id'));
        $this->select('customer_id', 'Farmer');

        // $this->select('type','Report Type')->options([
        //     'ledger'=>'ledger',
        //     'collection'=>'collection',
        //     'summary'=>'summary',
        // ]);

    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [];
    }
}
