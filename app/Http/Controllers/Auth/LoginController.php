<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request ->json()->all() ,[
            'email'=>'required|email|bail',
            'password'=>'required|min:6|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => "thông tin đăng nhập sai hoặc không đầy đủ",
            ],400);
        }
        $creadentials = $request ->json()->all();
        try{
            if(! $token = JWTAuth::attempt($creadentials)){
                return  response()->json([
                    "errors" => "thông tin đăng nhập không chính xác",
                ],400);
            }
        }catch(JWTException $e){
            return response([
                "message" => "token is valid",
            ],500);
        }
        $user = JWTAuth::user();
        $role = $user->roles->first();

        return response()->json([
            "user"=>$user->only("name","id","email"),
            "role"=>$role->name,
            "token"=>$token,
        ],200);
    }
    
}
