<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\LogoutAction;
use App\Models\Location;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\UserData;
use OpenAdmin\Admin\Facades\Admin;

class RetailerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Farmers';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new UserData());
        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            if (isAdmin()) {
                $filter->equal('location_id', __('Location'))->select(Location::all()->pluck('location_id', 'location_id'));
            }
            //... additional filter options
        });
        $grid->model()->orderBy('user_id', "desc");
        $grid->expandFilter();
        $grid->quickSearch(function ($model, $query) {
            $model->where('last_name', 'like', "%{$query}%");
        });
        if (!isAdmin()) {
            $grid->model()->where('location_id', Admin::user()->location_id);
        } else {
            $grid->column('location_id', "Location")->sortable();
        }
        $grid->column('status', __('Deleted'))->switch()->sortable();
        $grid->column('location_id', "Location")->sortable();
        $grid->column('id', __('Id'))->sortable();
        $grid->column('last_name', __('Name'))->sortable();
        $grid->column('contact', __('contact'))->sortable();
        $grid->column('route', __('User Area'))->sortable();
       




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
        $show->field('route', __('User Area'));
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
        $form->text('route', __('Area'));
        $form->text('contact', __('mobile'));
        $form->switch('status', __('Block'));
        if(is('admin')){
            $form->select('location_id', "Location")->options(Location::all()->pluck('location_id','location_id'))->default(Admin::user()->location_id);
        }else{
            $form->hidden('location_id', "Location")->default(Admin::user()->location_id);
        }
        return $form;
    }
}
