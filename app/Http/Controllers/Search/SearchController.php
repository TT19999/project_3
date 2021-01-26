<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Set;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchController extends Controller
{
    public function show(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $post = Set::with("user")->where('name','like','%'.$request->key_word.'%')
                ->orWhere('intro','like','%'.$request->key_word.'%')->orderByDesc('created_at')->get();
        return response()->json([
            "post" => $post,
        ],200);
    }
}
