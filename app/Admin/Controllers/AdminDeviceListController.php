<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\AdminDeviceList;

class AdminDeviceListController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'AdminDeviceList';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdminDeviceList());

        $grid->column('id', __('Id'));
        $grid->column('full_device_name', __('Full device name'));
        $grid->column('admin_id', __('Admin id'));
        $grid->column('block', __('Block'));
        $grid->column('ip_addresses', __('Ip addresses'));
        $grid->column('device_id', __('Device id'));
        $grid->column('status', __('Status'));
        $grid->column('last_accessed', __('Last accessed'));
        $grid->column('last_logout_at', __('Last logout at'));
        $grid->column('last_login_at', __('Last login at'));
        $grid->column('uuid', __('Uuid'));
        $grid->column('device_name', __('Device name'));
        $grid->column('device_model', __('Device model'));
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
        $show = new Show(AdminDeviceList::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('full_device_name', __('Full device name'));
        $show->field('admin_id', __('Admin id'));
        $show->field('block', __('Block'));
        $show->field('ip_addresses', __('Ip addresses'));
        $show->field('device_id', __('Device id'));
        $show->field('status', __('Status'));
        $show->field('last_accessed', __('Last accessed'));
        $show->field('last_logout_at', __('Last logout at'));
        $show->field('last_login_at', __('Last login at'));
        $show->field('uuid', __('Uuid'));
        $show->field('device_name', __('Device name'));
        $show->field('device_model', __('Device model'));
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
        $form = new Form(new AdminDeviceList());

        $form->text('full_device_name', __('Full device name'));
        $form->number('admin_id', __('Admin id'));
        $form->switch('block', __('Block'));
        $form->text('ip_addresses', __('Ip addresses'));
        $form->text('device_id', __('Device id'));
        $form->text('status', __('Status'));
        $form->datetime('last_accessed', __('Last accessed'))->default(date('Y-m-d H:i:s'));
        $form->datetime('last_logout_at', __('Last logout at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('last_login_at', __('Last login at'))->default(date('Y-m-d H:i:s'));
        $form->text('uuid', __('Uuid'));
        $form->text('device_name', __('Device name'));
        $form->text('device_model', __('Device model'));

        return $form;
    }
}
