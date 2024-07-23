<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController as ControllersOrderController;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Order;
use App\Models\User;
use App\Models\UserData;

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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_date_time', __('Order date time'));
        $show->field('bill_no', __('Bill no'));
        $show->field('shift', __('Shift'));
        $show->field('total', __('Total'));
        $show->items('Items', function ($items) {
            $items->setResource('/admin/order-items');

            $items->type();
            $items->fat();
            $items->snf();
            $items->litres();
            $items->clr();
            $items->amt();
            $items->rate();
        });
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
        $show->field('customer_id', __('Customer id'));
        $show->field('user_id', __('User id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());


        $form->text('total', __('Total'));

        $form->tab('Basic info', function ($form) {

            $form->radio('shift', __('Shift'))->options([
                'morning' => 'Morning',
                'evening' => 'Evening',
            ])->default('morning');
            $form->hidden('order_date_time', __('Order date time'))->default(date('Y-m-d H:i:s'));
            $form->hidden('bill_no', __('Bill no'))->default((new ControllersOrderController())->generateInvoice());
            $form->select('customer_id', __('Retailer'))->default(User::all()->pluck('name', 'id')->toArray());
        })->tab('Seo', function ($form) {

            $form->text('meta_title', __('Meta Title'));
            $form->textarea('meta_description', __('Meta Description'));
        });
        return $form;
    }
}
