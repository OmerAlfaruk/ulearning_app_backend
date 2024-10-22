<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function createUser(Request $request)
    {

        
        try {
            $validateUser = Validator::make($request->all(), [
               'avatar'=>'required',
               'type'=>'required',
               'open_id'=>'required',
               'name' => 'required',
               'email' => 'required',
              // 'password'=>'required|min:6'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            //validated will have all user field values
            //we can save in the database
            $validated = $validateUser->validated();
            $map = [];

            // email,phone,google,facebook,apple
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];
            $user = User::where($map)->first();

//checking connection
           


            //whether user has already logged in or not
            // empty means does not exist
            // then save the user for the first time in the database

            if (empty($user?->id)) {
            
                // this certain user has never been in our database
                // our job is to assign the user in the database
                // this token is user Id 
                $validated['token'] = md5(uniqid() . rand(1000, 99999));
                //user created for first time
                $validated['created_at'] = Carbon::now();
                
        // return response()->json(
        //     [
        //         'status'=>true,
        //         'data'=>'my data',
        //         'message'=>'new message'

        //     ],200
        // );
               // $validated['password'] = Hash::make($validated['password']); // Hash the password

                // return id of the row after saving
                $userID = User::insertGetId($validated);
                $userInfo = User::where('id', '=', $userID)->first();

                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;

                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token' => $accessToken]);

                return response()->json([
                    'code' => 200,
                    'msg' => 'User Created Successfully',
                    'data' => $userInfo
                ], 200);

            } 
                // If user already exists
                $accessToken = $user->createToken(uniqid())->plainTextToken;
                $user->access_token = $accessToken;
                User::where('open_id', '=', $validated['open_id'])->update(['access_token' => $accessToken]);


                return response()->json([
                    'code' => 200,
                    'msg' => 'User Logged in Successfully',
                    'data' => $user
                ], 200);
            

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password do not match our records.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
