<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\LogoutAction;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->quickSearch(function ($model, $query) {
            $model->where('name', 'like', "%{$query}%");
        });
        $grid->model()->orderBy('id', "desc");

        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('Name'))->sortable();
        $grid->column('email', __('Email'))->sortable();
        $grid->column('password', __('password'))->sortable();
        $grid->column('route', __('route'))->sortable();
        $grid->column('user_type', __('User type'))->sortable();
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
        $show = new Show(User::findOrFail($id));


        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('password', __('password'));
        $show->field('mobile', __('Mobile'));
        $show->field('route', __('route'));
        $show->field('user_type', __('User type'));
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
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->text('email', __('Email'));
        $form->text('password', __('password'));
        $form->number('route', __('Route'))->default('');
        $form->text('user_type', __('User type'));
        $form->switch('status', __('Block'));



        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}
