<?php

namespace App\Admin\Controllers;

use App\Exports\LedgetExport;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use App\Imports\LedgetImport;
use App\Models\Location;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Order;
use App\Models\RateCalculation;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use OpenAdmin\Admin\Grid\Filter;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Widgets\Box;
use OpenAdmin\Admin\Widgets\Table;

class LedgerController extends AdminController
{

    protected $title = 'Ledger';

    protected $controller;

    public function __construct()
    {
        $this->controller = new OrderController();
    }


    protected function grid($type = null)
    {
        $grid = new Grid(new Order());
        $this->title = "Ledger Reports";
        $grid->disableCreateButton();
        $grid->fixHeader();
        $grid->expandFilter();
        $grid->filter(function (Filter $filter) {
            $filter->disableIdFilter();
            if (isAdmin()) {
                $filter->equal('location_id', "VSP")->select(Location::all()->pluck('name', 'location_id'));
            }
             
            
          
            $filter->equal('customer_id', __('Farmer'))->select(UserData::all()->pluck('last_name', 'user_id'));
            $filter->between('order_date_time', 'Collection Date')->date();

        });
        
        return $this->controller->showGrid($grid);
    }



    protected function detail($id)
    {

        return $this->controller->detail($id);
    }







    protected function form()
    {
        $form = (new Form(new Order()));
        return $this->controller->form($form);
    }
}
