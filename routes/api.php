<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace'=>'Api'],function(){
    //Route::post('login', [UserController::class, 'createUser']);
    Route::post('login', 'UserController@createUser');
   // authentication middleware 
    Route::group(['middleware'=>['auth:sanctum']],function(){
        //Route::any('/courseList', [CourseController::class, 'courseList']);
        Route::any('/courseList', 'CourseController@courseList');
        Route::any('/courseDetail', 'CourseController@courseDetail');
        Route::any('/checkout', 'PayController@checkout');



    });
});

