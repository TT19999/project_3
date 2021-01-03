<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVerifyEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        dd($request->only('email','password'));
        if( Auth::attempt($request->only('email','password'))){
            $user=Auth::user();
            if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
                return response()->json([
                    "errors" => "user chua verify email",
                ],403);
            }
            else{
                return $next($request);
            }
        }
        else{
            return  response()->json([
                "errors" => "thông tin đăng nhập không chính xác",
            ],400);
        }
    }
}
