<?php
namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Form\Field\Display;
use Encore\Admin\Form\Field\Text;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use SebastianBergmann\LinesOfCode\Counter;
use Encore\Admin\Tree;

use function Laravel\Prompts\select;

class CourseController extends AdminController
{
    protected function grid()
    {
        $grid = new Grid(new Course());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('user_token', __('Teacher'))->display(function ($token){
            // for further processing data, you can create any method inside it or do operation

            return User::where('token','=',$token)->value('name'); });
       
            $grid->column('thumbnail', __('Thumbnail'))->image('/uploads/', 50, 50);
        $grid->column('description', __('Description'));
        $grid->column('video_length', __('Video length'));
        $grid->column('lesson_num', __('Lesson num'));
        $grid->column('downloadable_res', __('downloadable res'));
        $grid->column('created_at', __('Created at'));
        $grid->column('price', __('Price'));
       
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
        $show = new Show(Course::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        $show->field('price', __('Price'));
        $show->field('lesson_num', __('Lesson num'));
        $show->field('video_length', __('Video length'));
        $show->field('downloadable_res', __('Downloadable res'));
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
        $form = new Form(new Course());

     
        $form->text('name', __('Name'));
        $result=CourseType::pluck('title','id');
        
        $form->select('type_id', __('Category'))->options($result);
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        // file is used for video and other format like pdf/doc
        $form->file('video', __('Video'))->uniqueName();
        $form->textarea('description', __('Description'));
        // decimal for float
      
        $form->number('lesson_num', __('Lesson num'));
        $form->number('video_length', __('Video length'));
        $form->number('downloadable_res', __('Downloadable res'));
        //who is posting
        $result=User::pluck('name','token');
        $form->select('user_token',__('Teacher'))->options($result);
        $form->decimal('price', __('Price'));

        $form->display('created_at',__('Created at'));
        $form->display('updated_at',__('Updated at'));

        return $form;
    }


}
