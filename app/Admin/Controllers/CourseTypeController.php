<?php
namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use SebastianBergmann\LinesOfCode\Counter;
use Encore\Admin\Tree;

class CourseTypeController extends AdminController
{
    //actually to show the tree form of the menu
    public function index(Content $content){
        $tree=new Tree(new CourseType);
        return $content->header('Course Types')->body($tree);
    }

    // just for view
    protected function detail($id)
    {
        $show = new Show(CourseType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Category'));
        $show->field('description', __('Description'));
        $show->field('order',__("Order"));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        // $show->disableActions();
        // $show->disableCreateButton();
        // $show->disableExport();

        return $show;
    }
    // it gets called when you create a new form or edit a row or info
    protected function form() {
        $form = new Form(new CourseType());
        $form->select('parent_id', __('Parent Category'))->options((new CourseType())::selectOptions());
        $form->text('title',__("Title")); //similar to string in laravel
        $form->textarea('description',__('Description')); //similar to text
        $form->number('order',__('Order')); // similar to int
        return $form;
    }


}
