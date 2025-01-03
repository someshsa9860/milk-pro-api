<?php

namespace App\Admin\Controllers;

use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class UserController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.administrator');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());
        $grid->model()->where('username', '!=', 'somesh');
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
        });
        $grid->quickSearch('username','location_id','name');

        $grid->column('status', __('Block'))->switch()->sortable();
        $grid->column('can_edit_order_date', __('Show Date'))->switch()->sortable();
        $grid->column('can_edit_order', __('Order Edit Access'))->switch()->sortable();
        $grid->column('can_delete_order', __('Order Delete Access'))->switch()->sortable();
        $grid->column('max_devices', __('Max devices'))->text()->sortable();

        $grid->users()->display(function ($users) {
            $count = count(array_filter($users, function ($user) {
                return ($user['status'] === 'logged-in')&&($user['block'] === 0);
            }));
            return $count;
        });

        $grid->column('location_id', "Location")->sortable();
        $grid->column('username', trans('admin.username'))->sortable();
        $grid->column('name', trans('admin.name'))->sortable();
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label()->sortable();
        $grid->column('created_at', trans('admin.created_at'))->sortable();

        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));
        $show->users('Devices', function ($comments) {

            $comments->resource('/admin/admin-device-lists');
            $comments->id();
            $comments->full_device_name();
            // $comments->admin_id();
            $comments->block()->switch();
            $comments->ip_addresses();
            $comments->device_id();
            $comments->status();
            $comments->last_accessed();
            $comments->last_logout_at();
            $comments->last_login_at();
            $comments->uuid();
            $comments->device_name();
            $comments->device_model();
            $comments->created_at();
            $comments->updated_at();
    
            
            
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);


        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'))
            // ->when('vsp',function (Form $form){
            //     if (is('admin')) 
            //     {
            //         $form->select('location_id', "Location")->options(Location::all()->pluck('location_id', 'location_id'))->default(Admin::user()->location_id);
            //     }
            // })
        ;
        if (is('admin')) {
            $form->select('location_id', "Location")->options(Location::all()->pluck('location_id', 'location_id'))->default(Admin::user()->location_id);
        } else {
            $form->hidden('location_id', "Location")->options(Location::all()->pluck('location_id', 'location_id'))->default(Admin::user()->location_id);
        }

        // $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));
        // $form->select('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));
        $form->switch('status', __('Block'))->default(0);
        $form->number('max_devices', __('Max devices'))->default(2);
        $form->switch('can_edit_order_date', __('Show Date'))->default(1);
        $form->switch('can_edit_order', __('Allow Edit Order'))->default(1);
        $form->switch('can_delete_order', __('Allow Delete Order'))->default(1);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }
}
