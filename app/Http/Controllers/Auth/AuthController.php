<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Verify;
use App\Notifications\ForgotNotification;
use App\Notifications\NewAccountNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotifycation;
use http\Env\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request ->json()->all() ,[
            'email'=>'required|email|bail',
            'password'=>'required|min:6|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => "Email hoặc mật khẩu không chính xác",
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
        $avatar = $user->profile->avatar;
        return response()->json([
            "user"=>$user->only("name","id","email"),
            "role"=>$role->name,
            "token"=>$token,
            "avatar"=>$avatar,
        ],200);
    }


    public function register(Request $request){
        $validator = Validator::make($request ->json()->all() ,[
            'first_name'=>'required|string|bail',
            'last_name'=>'required|string|bail',
            'email'=>'required|email|unique:users|bail',
            'password'=>'required|min:6|confirmed|bail',
            'subject'=>'required|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->getMessageBag()->first(),
            ],400);
        }
        $user = User::create([
            'name'=> $request->input('first_name') . $request->input('last_name'),
            'email'=> $request->input('email'),
            'password'=> Hash::make($request->input('password')),
        ]);
        $user->profile()->create([
            'first_name' => $request->input('first_name'),
            'last_name' =>$request->input('last_name'),
            'subject' => $request->input('subject'),
        ]);
        DB::table('role_user')->insert([
            'user_id'=>$user->id,
            'role_id'=>2,
        ]);
        $token = rand(100000,999999);
        $user->notify(new VerifyEmailNotifycation($token));
//        $user->sendEmailVerificationNotification();
        return response()->json([
            "user" =>$user,
            "role"=> "user",
        ],201);
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request ->json()->all() ,[
            'old_password'=>'required|min:6|bail',
            'new_password'=>'required|min:6|confirmed|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->getMessageBag()->first(),
            ],400);
        }
        $user = JWTAuth::user();
        if(Hash::check($request->input("old_password"), $user->password)){
            $user->password= Hash::make($request->input("new_password"));
            $user->save();
            $user->notify(new ResetPasswordNotification());
            return response()->json([
                "message"=>"Thay đổi mật khẩu thành công"
            ],201);
        }
        else{
            return response()->json([
                "errors" => "Old Password is not true",
            ],400);
        }
    }

    public function forgotEmail(Request $request){
        $validator = Validator::make($request ->json()->all() ,[
            'email'=>'unique:users',
        ]);
        if($validator->fails()) {
            $user = User::where("email","=",$request->email)->first();
            $new_password = Str::random(10);
            $user->update([
                "password" => Hash::make($new_password),
            ]);
            $user->notify(new ForgotNotification($new_password));
            return response()->json([
                "message" => "mat khau moi da duoc gui, vui long check email"
            ],201);
        }
        else {
            return response()->json([
                'errors' => "Email không chính xác",
            ], 400);
        }
    }

    public function  adminLogin(Request $request){
        return $this->login($request);
    }

    // public function verifyEmail(Request $request){
    //     $validator = Validator::make($request ->json()->all() ,[
    //         'email'=>'required|email|bail',
    //         'token'=>'required|max:255|bail',
    //     ]);
    //     if($validator->fails()){
    //         return response()->json([
    //             'errors' => $validator->errors()->getMessageBag()->first(),
    //         ],400);
    //     }
    //     $verifyEmail=Verify::query()->where('email','=',$request->email)->first();
    //     if($verifyEmail->token == $request->token){
    //         DB::table('users')->where('email','=',$request->email)->update([
    //             'email_verified_at'=>now(),
    //         ]);
    //         $verifyEmail->delete();
    //         return response()->json([
    //             "message" => "verify thanh cong",
    //         ],200);

    //     }
    //     else return response()->json([
    //         'errors' => "ma code khong chinh xac",
    //     ],400);
    // }

    public function verify(Request $request){
        $user=User::query()->find($request->input('id'));
        $user->markEmailAsVerified();
        return redirect('http://localhost:3000/login');
    }
}
