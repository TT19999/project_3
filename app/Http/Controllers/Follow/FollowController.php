<?php

namespace App\Http\Controllers\Follow;

use App\Http\Controllers\Controller;
use App\Jobs\FollowNotificationJob;
use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class FollowController extends Controller
{
    public function create(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request ->json()->all() ,[
            'id'=>'unique:users'
        ]);
        if($validator->fails()){
            if(!$user->hasFollower($request->id)){
                Follower::query()->insert([
                    'user_id'=>$user->id,
                    'follower_id'=>$request->id,
                ]);
                $follower = User::query()->find($request->id);
                $job = (new FollowNotificationJob($follower,$user));
                dispatch($job);
                return response()->json([
                    'message' => 'done',
                ],201);
            }
            else return  response()->json([
                'errors' => "da follow roi",
            ],400);
        }
        return response()->json([
            'errors' => "Khong ton tai",
        ],404);
    }

    public function delete(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request ->json()->all() ,[
            'id'=>'unique:users'
        ]);
        if($validator->fails()){
            if($user->hasFollower($request->id)){
                Follower::query()->where('user_id','=',$user->id)->where('follower_id','=',$request->id)->delete();
                return response()->json([
                    'message' => 'done',
                ],204);
            }
            else return  response()->json([
                'errors' => "ban khong follow ng nay",
            ],400);
        }

        return response()->json([
            'errors' => "Khong ton tai",
        ],404);
    }
}
