<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\OrderItem;

class OrderItemController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'OrderItem';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OrderItem());

        $grid->column('id', __('Id'));
        $grid->column('order_id', __('Order id'));
        $grid->column('user_id', __('User id'));
        $grid->column('customer_id', __('Customer id'));
        $grid->column('type', __('Type'));
        $grid->column('fat', __('Fat'));
        $grid->column('snf', __('Snf'));
        $grid->column('litres', __('Litres'));
        $grid->column('price', __('Price'));
        $grid->column('shift', __('Shift'));
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
        $show = new Show(OrderItem::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('Order id'));
        $show->field('user_id', __('User id'));
        $show->field('customer_id', __('Customer id'));
        $show->field('type', __('Type'));
        $show->field('fat', __('Fat'));
        $show->field('snf', __('Snf'));
        $show->field('litres', __('Litres'));
        $show->field('price', __('Price'));
        $show->field('shift', __('Shift'));
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
        $form = new Form(new OrderItem());

        $form->number('order_id', __('Order id'));
        $form->number('user_id', __('User id'));
        $form->number('customer_id', __('Customer id'));
        $form->text('type', __('Type'));
        $form->decimal('fat', __('Fat'));
        $form->decimal('snf', __('Snf'));
        $form->decimal('litres', __('Litres'));
        $form->decimal('price', __('Price'));
        $form->text('shift', __('Shift'));

        return $form;
    }
}
