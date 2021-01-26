<?php

namespace App\Http\Controllers\Set;

use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetController extends Controller
{
    public function index(){
        $sets = Set::with("user")->withCount('cards','comments')->with("categories")->get();
        return response()->json([
            "sets" => $sets,
        ],200);
    }

    public function create(Request  $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $validator = Validator::make($request ->json()->all() ,[
            'intro'=>'required|string|max:255|bail',
            'name'=>'required|string|max:255|bail',
            'status' => 'required|string|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()->getMessageBag()->first(),
            ],400);
        }
        $set = $user->sets()->create(
            $request ->json()->all()
        );
        return response()->json([
            'message' => 'done',
            'id' => $set->id
        ],201);
    }

    public function show(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $validator = Validator::make($request->all() ,[
            'id'=>'string|unique:sets',
        ]);
        if($validator->fails()){
            $sets=Set::query()->with('cards','comments','user')->withCount('comments','cards')->find($request->id);
            $owner= User::query()->withCount('sets','follower')->where('id','=',$sets->user_id)->first();
            return response()->json([
                'sets'=>$sets,
                "owner"=>$owner,
                "is_follower" => $user->hasFollower($owner->id),
            ],200);
        }
        return response()->json([
            'error' => 'not found',
        ],400);
    }

    public function edit(){

    }

    public function update(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $set =Set::query()->findOrFail($request->id);
            if($user->can('delete',$set)){
                $set->update($request->all());
                return response()->json([
                    'message' => 'done',
                ],203);
            }
        return response()->json([
            'error' => 'can do this action',
        ],403);
    }

    public function delete(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $set =Set::query()->findOrFail($request->id);
        if($user->can('delete',$set)){
            $set->delete();
            return response()->json([
                'message' => 'done',
            ],203);
        }
        return response()->json([
            'error' => 'can do this action',
        ],403);
    }
}
