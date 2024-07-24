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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

    $form->tab('Basic info', function ($form) {
        $form->radio('shift', __('Shift'))->options([
            'morning' => 'Morning',
            'evening' => 'Evening',
        ])->default('morning');
        $form->hidden('order_date_time', __('Order date time'))->default(date('Y-m-d H:i:s'));
        $form->hidden('bill_no', __('Bill no'))->default((new ControllersOrderController())->generateInvoice());
        $form->select('customer_id', __('Retailer'))->options(UserData::all()->pluck('last_name', 'user_id'));
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
        $this->storeIgnoredValues($form, 'cow_');
        $this->storeIgnoredValues($form, 'mixed_');
        $this->storeIgnoredValues($form, 'buffalo_');

        $this->ignore($form, 'cow_');
        $this->ignore($form, 'mixed_');
        $this->ignore($form, 'buffalo_');
    });

    // callback before save
    $form->saving(function (Form $form) {
        $this->storeIgnoredValues($form, 'cow_');
        $this->storeIgnoredValues($form, 'mixed_');
        $this->storeIgnoredValues($form, 'buffalo_');

        $this->ignore($form, 'cow_');
        $this->ignore($form, 'mixed_');
        $this->ignore($form, 'buffalo_');
    });

    // callback after save
    $form->saved(function (Form $form) {
        $cowValues = $this->retrieveIgnoredValues('cow_');
        $buffaloValues = $this->retrieveIgnoredValues('buffalo_');
        $mixedValues = $this->retrieveIgnoredValues('mixed_');

        // Now you can use $cowValues, $buffaloValues, and $mixedValues as needed
        Log::channel('callvcal')->info(json_encode([
            'cow' => $cowValues,
            'buffalo' => $buffaloValues,
            'mixed' => $mixedValues,
        ]));
    });

    return $form;
}

protected function storeIgnoredValues(Form $form, $key)
{
    $values = [
        'litres' => $form->{$key . 'litres'},
        'fat' => $form->{$key . 'fat'},
        'clr' => $form->{$key . 'clr'},
        'snf' => $form->{$key . 'snf'},
        'rate' => $form->{$key . 'rate'},
        'amt' => $form->{$key . 'amt'},
    ];

    Session::put($key . 'values', $values);
}

protected function retrieveIgnoredValues($key)
{
    return Session::pull($key . 'values', []);
}

function run($key, $form)
{
    $form->number($key . 'litres', __('Qty'));
    $form->number($key . 'fat', __('fat'));
    $form->number($key . 'clr', __('clr'));
    $form->number($key . 'snf', __('snf'));
    $form->number($key . 'rate', __('rate'));
    $form->number($key . 'amt', __('amt'));
}

function ignore($form, $key)
{
    $form->ignore($key . 'litres');
    $form->ignore($key . 'fat');
    $form->ignore($key . 'clr');
    $form->ignore($key . 'snf');
    $form->ignore($key . 'rate');
    $form->ignore($key . 'amt');
}

}
