<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    // return all course Liost
    public function courseList(){
        
      try{
        $result=Course::select('name','thumbnail','lesson_num','price','id')->get();
        return response()->json(
        [
            'code'=>200,
            'msg'=>'My course list is here',
            'data'=>$result

        ],200);
      }catch(\Throwable $throw){
        return response()->json(
            [
                'code'=>500,
                'msg'=>'exeption',
                'data'=>$throw->getMessage()
    
            ],200);
      }
    }
     // return all course Liost
     public function courseDetail(Request $request) {
      $id = $request->id;
  
      // Validate that the 'id' is provided and is a valid integer
      if (!$id || !is_numeric($id)) {
          return response()->json([
              'code' => 400,
              'msg' => 'Invalid course ID provided',
              'data' => null
          ], 400);
      }
  
      try {
          // Fetch the course details by ID
          $result = Course::where('id', $id)
              ->select(
                  'id',
                  'name',
                  'user_token',
                  'description',
                  'price',
                  'lesson_num',
                  'downloadable_res',
                  'video_length',
                  'thumbnail'
              )->first();
  
          if (!$result) {
              return response()->json([
                  'code' => 404,
                  'msg' => 'Course not found',
                  'data' => null
              ], 404);
          }
  
          return response()->json([
              'code' => 200,
              'msg' => 'Course details retrieved successfully',
              'data' => $result
          ], 200);
  
      } catch (\Exception $e) {
          // Catch more specific exceptions if necessary
          return response()->json([
              'code' => 500,
              'msg' => 'An error occurred while fetching course details',
              'data' => $e->getMessage()
          ], 500);
      }
  }
  
    
}
