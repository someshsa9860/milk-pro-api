<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\LogoutAction;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\RateList;
use Illuminate\Support\Facades\Hash;

class RateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'RateList';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new RateList());
        

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
        $show = new Show(RateList::findOrFail($id));


        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('password', __('password'));
        $show->field('mobile', __('Mobile'));
        $show->field('route', __('route'));
        $show->field('RateList_type', __('RateList type'));
        $show->field('status', __('status'));
        $show->field('updated_at', __('Updated at'));
        $show->field('created_at', __('Created at'));


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RateList());

        $form->text('name', __('Name'));
        $form->text('email', __('Email'));
        $form->text('password', __('password'));
        $form->number('route', __('Route'))->default('');
        $form->text('RateList_type', __('RateList type'));
        $form->switch('status', __('Block'));



        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}
