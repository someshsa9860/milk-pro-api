<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\Dashboard;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        // Fetching counts for different data points
        $adminsCount = DB::table('admin_users')->count();
        $farmersCount = UserData::count();
    
        // Fetching order-related statistics
    
        $statistics = [
            [
                'title' => 'Collection Orders by Location',
                'description' => 'This chart shows the total number of orders by location.',
                'labelX' => 'Location',
                'labelY' => 'Collection',
                'data' => Order::where('is_sell',0)->selectRaw('location_id, COUNT(*) as total')
                    ->groupBy('location_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'location_id' => $item->location_id,
                            'total' => $item->total
                        ];
                    })
                    ->toArray(),
            ],
            [
                'title' => 'Sells by Location',
                'description' => 'This chart shows the total number of sells by location.',
                'labelX' => 'Location',
                'labelY' => 'Sells',
                'data' => Order::where('is_sell',1)->selectRaw('location_id, COUNT(*) as total')
                    ->groupBy('location_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'location_id' => $item->location_id,
                            'total' => $item->total
                        ];
                    })
                    ->toArray(),
            ],
            [
                'title' => 'Farmers by Location',
                'description' => 'This chart shows the total number of farmers by location.',
                'labelX' => 'Location',
                'labelY' => 'Farmers',
                'data' => UserData::selectRaw('location_id, COUNT(*) as total')
                    ->groupBy('location_id')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'location_id' => $item->location_id,
                            'total' => $item->total
                        ];
                    })
                    ->toArray(),
            ],
            // Add more statistics with different titles, descriptions, labels, and data as needed.
        ];
    
        // Organizing data to be passed to the view
        $data = [
            'counts' => [
                ['name' => "VSP", 'count' => $adminsCount],
                ['name' => "Farmers", 'count' => $farmersCount],
                ['name' => "Milk Collection Count", 'count' => Order::where('is_sell', 0)->count()],
                ['name' => "BMC (INR)", 'count' => Order::where('is_sell', 0)->sum('buffalo_amt')],
                ['name' => "CMC (INR)", 'count' => Order::where('is_sell', 0)->sum('cow_amt')],
                ['name' => "MMC (INR)", 'count' => Order::where('is_sell', 0)->sum('mixed_amt')],
                ['name' => "Sell Count", 'count' => Order::where('is_sell', 1)->count()],
                ['name' => "Sell Amount (INR)", 'count' => Order::where('is_sell', 1)->sum('payment')],
            ],
            'statistics' => $statistics,
        ];
    
        // Rendering content with CSS and passing data to the view
        return $content
            ->css_file(Admin::asset("open-admin/css/pages/dashboard.css"))
            ->title('Dashboard')
            ->description('Summary of system statistics')
            ->body(view('index', compact('data')));
    }
    
}
