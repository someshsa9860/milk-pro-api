<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController as ControllersOrderController;
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
use OpenAdmin\Admin\Widgets\Table;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Order';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());

        $grid->column('id', __('Id'));
        $grid->column('order_date_time', __('Order date time'));
        $grid->column('bill_no', __('Bill no'));
        $grid->column('shift', __('Shift'));
        $grid->column('total', __('Total'));
        $grid->column('customer_id', __('Customer id'));
        $grid->column('user_id', __('User id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $order = Order::findOrFail($id);
        $show = new Show($order);

        $show->field('id', __('Id'));
        $show->field('order_date_time', __('Order date time'));
        $show->field('bill_no', __('Bill no'));
        $show->field('shift', __('Shift'));
        $show->field('total', __('Total'));
        $show->customer('Customer', function ($items) {

            $items->user_id();
            $items->route();
            $items->last_name();
            $items->add1();
            $items->contact();
            $items->amount();
            $items->crate();
            $items->type();
        });
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('')->as(function ($v) use ($order) {
            $headers = ['Milk', 'Cow', "Buffalo", "Mixed"];
            $rows = [
                ["qty", $order->cow_qty ?? 0, $order->buffalo_qty ?? 0, $order->mixed_qty ?? 0],
                ["fat", $order->cow_fat ?? 0, $order->buffalo_fat ?? 0, $order->mixed_fat ?? 0],
                ["snf", $order->cow_snf ?? 0, $order->buffalo_snf ?? 0, $order->mixed_snf ?? 0],
                ["clr", $order->cow_clr ?? 0, $order->buffalo_clr ?? 0, $order->mixed_clr ?? 0],
                ["rate", $order->cow_rate ?? 0, $order->buffalo_rate ?? 0, $order->cmixedrate ?? 0],
                ["amt", $order->cow_amt ?? 0, $order->buffalo_amt ?? 0, $order->mixed_amt ?? 0],
            ];


            $table = new Table($headers, $rows, ["border" => "1px solid #FFAA00", "margin" => "20px"]);

            echo "<div style=\" padding-left:2em;padding-right:2em; padding-top:1em; \">" . $table->render() . "</div>";
        });



        return $show;
    }

    function showRun($key, Show $show)
    {
        $show->field($key . 'litres', __('Qty'));
        $show->field($key . 'fat', __('fat'));
        $show->field($key . 'snf', __('snf'));
        $show->field($key . 'clr', __('clr'));
        $show->field($key . 'rate', __('rate'));
        $show->field($key . 'amt', __('amt'));
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */

    protected function form()
    {
        $form = new Form(new Order());

        $form->tab('Basic info', function (Form $form) {
            $form->radio('shift', __('Shift'))->options([
                'morning' => 'Morning',
                'evening' => 'Evening',
            ])->default('morning')->required();
            $form->hidden('order_date_time', __('Order date time'))->default(date('Y-m-d H:i:s'));
            $form->hidden('bill_no', __('Bill no'))->default((new ControllersOrderController())->generateInvoice());
            $form->select('customer_id', __('Retailer'))->options(UserData::all()->pluck('last_name', 'user_id'))->required();
            $form->number('total', __('Total Amount (Optional, auto-calculated if not set)'));
        })->tab('Cow', function ($form) {
            $key = 'cow_';
            $this->run($key, $form);
        })->tab('Buffalo', function ($form) {
            $key = 'buffalo_';
            $this->run($key, $form);
        })->tab('Mixed', function ($form) {
            $key = 'mixed_';
            $this->run($key, $form);
        });

        $form->submitted(function (Form $form) {
             });

        // callback before save
        $form->saving(function (Form $form) {
            Log::channel("callvcal")->info(json_encode([
                'cow'=>[
                    'initial'=>[
                        'litres'=>$form->cow_litres,
                        'fat'=>$form->cow_fat,
                        'snf'=>$form->cow_snf,
                        'amt'=>$form->cow_amt,
                    ]
                ]
            ]));
            $cow = $this->calculate('cow_', $form);
            $buffalo = $this->calculate('buffalo_', $form);
            $mixed = $this->calculate('mixed_', $form);
            $this->set('cow_', $form, $cow);
            $this->set('buffalo_', $form, $buffalo);
            $this->set('mixed_', $form, $mixed);
            Log::channel("callvcal")->info(json_encode([
                'cow'=>[
                    'final'=>[
                        'litres'=>$form->cow_litres,
                        'fat'=>$form->cow_fat,
                        'snf'=>$form->cow_snf,
                        'amt'=>$form->cow_amt,
                    ]
                ]
            ]));

            $form->total = ($form->cow_amt ?? 0) + ($form->buffalo_amt ?? 0) + ($form->mixed_amt ?? 0);
       
        });

        // callback after save
        $form->saved(function (Form $form) {
        });

        return $form;
    }


    function run($key, Form $form)
    {
        $form->number($key . 'litres', __('Qty, (Required)'));
        $form->number($key . 'fat', __('fat, (Required)'));
        $form->number($key . 'snf', __('snf, (Required)'));
        $form->number($key . 'clr', __('clr'));
        $form->number($key . 'rate', __('rate'));
        $form->number($key . 'amt', __('amt'));
    }
    function set($key, Form $form, $cal)
    {
        $form->{$key . 'litres'} = $cal->litres;
        $form->{$key . 'fat'} = $cal->fat;
        $form->{$key . 'snf'} = $cal->snf;
        $form->{$key . 'clr'} = $cal->clr;
        $form->{$key . 'rate'} = $cal->rate;
        $form->{$key . 'amt'} = $cal->amt;
    }

    function calculate($key, Form $form)
    {
        $cal = new RateCalculation(
            litres: $form->{$key . "litres"},
            snf: $form->{$key . "snf"},
            rate: $form->{$key . "rate"},
            clr: $form->{$key . "clr"},
            amt: $form->{$key . "amt"},
            fat: $form->{$key . "fat"}

        );

        return $cal;
    }
}
