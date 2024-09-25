<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use \App\Models\Order;

class CollectionSummaryController extends AdminController
{

    protected $title = 'MPP collection summary';

    protected $controller;

    public function __construct()
    {
        $this->controller = new OrderController();
    }


    protected function grid($type = null)
    {
        $grid = new Grid(new Order());
        $grid->disableCreateButton();
        $grid->fixHeader();
        $grid->expandFilter();
        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            $filter->equal('shift', __('Shift'))->select([
                'morning' => 'Morning',
                'evening' => 'Evening',
            ]);
            $filter->date('order_date_time', __('Order Date'));

            //... additional filter options
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
