<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\LogoutAction;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\UserData;

class RetailerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Retailers';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserData());
        $grid->quickSearch(function ($model, $query) {
            $model->where('name', 'like', "%{$query}%");
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('last_name', __('Name'))->sortable();
        $grid->column('contact', __('contact'))->sortable();
        $grid->column('type', __('User type'))->sortable();
        $grid->switch('status', __('status'))->sortable();

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
        $show = new Show(UserData::findOrFail($id));


        $show->field('user_id', __('Id'));
        $show->field('last_name', __('Name'));
        $show->field('contact', __('mobile'));
        $show->field('add1', __('Address'));
        $show->field('type', __('User type'));
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
        $form = new Form(new UserData());

        $form->text('last_name', __('Name'));
        $form->text('contact', __('mobile'));
        $form->text('add1', __('Address'));
        $form->hidden('route', __('Address'))->default(1);
        $form->text('type', __('User type'))->default("retailer");
        $form->switch('status', __('Block'));
        return $form;
    }
}
