<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {   
            $user = JWTAuth::parseToken()->authenticate();
            
        } catch (Exception $e) {
            return \response() ->json(
                [
                    "errors"=>"token is not valid"
                ],401
            );
        }
        return $next($request);
    }
}
