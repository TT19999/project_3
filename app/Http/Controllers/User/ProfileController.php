<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function index(){
        $user = JWTAuth :: parseToken() ->authenticate();
        return response()->json([
            "user"=>$user->only("name","email"),
            "profile"=> $user->profile,
            "action"=>"edit",
        ],200);
    }


    public static function show(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $userShow = User::find($request->user_id);
        if($userShow != null ){
            $profile = $userShow->profile;
            if($user->can('view', $profile)){
                return response()->json([
                    "user"=>$userShow->only("name","email"),
                    "profile"=>$profile,
                    "skill" => $userShow->skills,
                    "action"=>"show",
                    "status"=>"public",
                ],200);
            }
            return response()->json([
                "errors"=>"Thông tin cá nhân không công khai",
            ],500);
        }
        else {
            return response()->json([
                'errors' => "người dùng không tồn tại",
            ],400);
        }
    }

    public function update(Request $request){

        // dd($request->toArray());
        $user = JWTAuth::parseToken() ->authenticate();
        // $user->skills()->sync(1);
        // dd($user->skills);
        $validator = Validator::make($request ->json()->all() ,[
            'first_name'=>'required|string|bail',
            'last_name'=>'required|string|bail',
            'status' => 'required|string|bail',
            'subject' => 'required|string|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->getMessageBag()->first(),
            ],400);
        }

        $user->profile->update($request->all());
        // $user->skills()->sync($request->skill);
        $user->name = $request->first_name .' '. $request->last_name;
        $user->save();
        return response()->json([
            "message" => "sửa thông tin thành công",
        ],201);
    }

    public function updateAvatar(Request  $request) {
        $user = JWTAuth::parseToken()->authenticate();
            if($request->hasFile('avatar')){
                $fileName = time().'_'.Str::random(10);
                $path = Storage::putFileAs('avatar', $request->avatar,$fileName);
                $profile = $user->profile;
                $profile->avatar = "https://kaopiz-final.s3-ap-southeast-1.amazonaws.com/".$path;
                $profile->save();
                $user->avatar="https://kaopiz-final.s3-ap-southeast-1.amazonaws.com/".$path;
                $user->save();
                return \response()->json([
                    'message' => 'Sua avatar thanh cong',
                    'avatar' => $profile->avatar,
                ],201);
            }else {
                return response()->json([
                    "errors" => "file avatar khong dung",
                ],400);
            }
    }
}
