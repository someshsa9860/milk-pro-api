<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Location;

class LocationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Location';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Location());
        $grid->quickSearch('location_name','location_id');

        $grid->column('id', __('Id'))->sortable();
        $grid->column('location_id', __('Location id'))->sortable();
        $grid->column('location_name', __('Location Name'))->sortable();
        $grid->column('created_at', __('Created at'))->sortable();
        $grid->column('updated_at', __('Updated at'))->sortable();

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
        $show = new Show(Location::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('location_id', __('Location id'));
        $show->field('location_name', __('Location Name'));
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
        $form = new Form(new Location());

        $form->text('location_id', __('Location (Don;t change once set)'))->rules('unique:locations')->required();
        $form->text('location_name', __('Location Name'))->rules('unique:locations')->required();

        return $form;
    }
}
