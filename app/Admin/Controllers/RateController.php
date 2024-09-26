<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\LogoutAction;
use App\Admin\Forms\RateImportForm;
use App\Models\Location;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\RateList;
use Illuminate\Support\Facades\Hash;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;

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
        if (!isAdmin()) {
            $grid->model()->where('location_id', Admin::user()->location_id);
        } else {
            $grid->column('location_id', "Location");
            // $grid->model()->where('location_id', Admin::user()->location_id);
        }
        $grid->model()->orderBy('srl', "desc");
        $grid->expandFilter();

        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();

            // Add a column filter
            if (isAdmin()) {
                $filter->equal('location_id', __('Location'))->select(Location::all()->pluck('location_id', 'location_id'));
            }
            $filter->equal('snf', __('snf'));;
            $filter->equal('fat', __('fat'));;

            //... additional filter options
        });
        $grid->column('srl',"SRL")->text()->sortable();
        $grid->column('snf',"SNF")->text()->sortable();
        $grid->column('fat',"FAT")->text()->sortable();
        $grid->column('rate',"RATE")->text()->sortable();
        $grid->column('cow',"Cow Rate")->text()->sortable();
        $grid->column('buffalo',"Buffalo Rate")->text()->sortable();
        $grid->column('mixed',"Mixed Rate")->text()->sortable();
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


        $show->field('srl', __('SRL'));
        $show->field('snf', __('SNF'));
        $show->field('fat', __('FAT'));
        $show->field('rate', __('Rate'));




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

        $form->text('fat', __('FAT'))->required();
        $form->text('snf', __('SNF'))->required();
        $form->text('rate', __('Rate'))->required();
        if(is('admin')){
            $form->select('location_id', "Location")->options(Location::all()->pluck('location_id','location_id'))->default(Admin::user()->location_id)->required();
        }else{
            $form->hidden('location_id', "Location")->default(Admin::user()->location_id);
        }
        return $form;
    }

    public function import(Content $content)
    {
        return $content
            ->title('Import Rate charts')
            ->body(new RateImportForm());
    }
}
