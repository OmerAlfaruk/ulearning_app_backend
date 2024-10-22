<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Checkout\Session;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Stripe\Climate\Order as ClimateOrder;
use Stripe\Stripe;

class PayController extends Controller
{
   public function checkout(Request $request) {
Try{
    $user=$request->user();
    $token=$user->token;
    $courseId=$request->id;

    /* Stripe api key*/
    Stripe::setApiKey('sk_test_51QAGTvJBQUr8IAnl7Lv5HqDBaO3n20DJQsoIASFZv8zxqlFkhSr89PAwKwuGc9Q9HAN3nwraY0z4YhxRvfJkCpE000LaIL9hX7');

    $courseResult=Course::where('id','=',$courseId)->first();

    if(empty($courseResult)){
        return response()->json([
            'code'=>400,
            'msg'=>'Course does not exist',
            'data'=>''
        ]);
    }
    $orderMap=[];
    $orderMap['course_id']=$courseId;
    $orderMap['user_token']=$token;
    $orderMap['status']=1;

    /* 
     if the order has been placed before or not so
      we need Order model/table
    */

    $ordeRes=Order::where($orderMap)->first();

    if(!empty($ordeRes)){
        return response()->json([
            'code'=>400,
            'msg'=>"You already bought this course",
            'data'=>$ordeRes
        ],400);
    }
    // new order for the user and lets's submit
    $YOUR_DOMAIN=env('APP_URL');
    $map=[];
    $map['user_token']=$token;
    $map['course_id']=$courseId;
    $map['total_amount']=$courseResult->price;
    $map['status']=0;
    $map['created_at']=Carbon::now();
    $orderNum=Order::insertGetId($map);
//create payment session

$checkOutSession=Session::create([
    'line_items'=>[['price_data'=>['currency'=>'USD',
    'product_data'=>[
        'name'=>$courseResult->name,
        'description'=>$courseResult->description,
    ],'unit_amount'=>intval(($courseResult->price)*100),],'quantity'=>1,],],
    'payment_intent_data'=>[
        'metadata'=>['order_num'=>$orderNum,'user_token'=>$token]
    ],
    'metadata'=>['order_num'=>$orderNum,'user_token'=>$token],
    'mode'=>'payment',
    'success_url'=>$YOUR_DOMAIN.'success',
    'cancel_url'=>$YOUR_DOMAIN.'cancel',
    ]

);

//returning stipe url
   return response()->json([
        'code'=>200,
        'msg'=>'success',
        'data'=> $checkOutSession->url,
    ],200);
}

catch(\Throwable $th){
    return response()->json([
        'error'=>$th->getMessage(),

    ],500);

}
   }
}
