<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
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

        return response()->json([
            "user" =>$user,
            "role"=> "user",
        ],201);
    }
}
