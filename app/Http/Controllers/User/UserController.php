<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Helper\Helper;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index(){
        $user = JWTAuth::parseToken() ->authenticate();
        if($user->can("viewAny",Profile::class)){
            $users=User::with("profile")->withTrashed()->get();
            return response()->json([
                "users" => $users,
            ],200);
        }
        else return response()->json([
             "errors" => "You can do this action",
        ],403);
    }

    public function show(Request  $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $userShow = User::with("skills")->with("profile")->find($request->id);
        if($userShow != null ){
            $profile = $userShow->profile;
            if($user->can('view', $profile)){
                return response()->json([
                    "user"=>$userShow,
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

    public function restore(Request $request){
        $validator = Validator::make( $request->all(),[
            'id'=>'unique:users',
        ]);
        if($validator->fails()){
            $user= User::onlyTrashed()->find($request->id);
            $user->restore();
            return response()->json([
                "message" => "khoi phuc nguoi dung thanh cong"
            ],200);
        }
        return response()->json([
            'errors' => "Khong co nguoi dung",
        ],400);
    }

    public function delete(Request $request){
        $validator = Validator::make( $request->all(),[
            'id'=>'unique:users',
        ]);
        if($validator->fails()){
            $user= User::find($request->id);
            $user->delete();
            return response()->json([
                "message" => "xoa nguoi dung thanh cong"
            ],200);
        }
        return response()->json([
            'errors' => "Khong co nguoi dung",
        ],400);
    }

}
