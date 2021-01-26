<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Jobs\CommentNotificationJob;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends Controller
{
    public function index(Request $request){
        $comments = Comment::with("user:id,name,avatar")->where('set_id','=',$request->input("set_id"))->get();
        return response()->json([
            'comments' => $comments,
        ],200);
    }

    public function create(Request $request,$id){
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request ->json()->all() ,[
            'set_id'=>'required|bail|numeric',
            'comment'=> 'required|string|bail'
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => "Thong tin chua chinh xac",
            ],400);
        }
        $post=Set::query()->find($id);
        if($user->can('view', $post)){
            $comment=$post->comments()->create([
                "comment"=>$request->input('comment'),
                "user_id"=>$user->id,
            ]);
            if($user->id != $post->user_id){
                $job = (new CommentNotificationJob($user,$post));
                dispatch($job);
            }
            return response()->json([
                "comment"=>$comment,
            ],201);
        }
        else return response()->json([
            "errors"=>"khong the comment",
        ],403);
    }

    public function update(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request ->json()->all() ,[
            'id'=> 'required|bail',
            'comment' => 'required|string|bail',
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => "Thong tin chua chinh xac",
            ],400);
        }
        $comment = Comment::query()->find($request->id);
//        dd($user->id,$comment->user_id);
            $comment->comment = $request->comment;
            $comment->save();
            return response()->json([
                "comment"=>$comment,
            ],201);

//        else return response()->json([
//            "errors" => "khong the xoa binh luan nay"
//        ],403);
    }

    public function delete(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->json()->all() ,[
            'id'=> 'required|bail'
        ]);
        if($validator->fails()){
            return response()->json([
                'errors' => "Thong tin chua chinh xac",
            ],400);
        }
        $comment = Comment::query()->find($request->id);
        if($user->can('delete',$comment)){
            $comment->delete();
            return response()->json([
                "message" => "done",
            ],204);
        }
        else return response()->json([
            "errors" => "khong the xoa binh luan nay"
        ],403);
    }

    public function all(){
        $comments = Comment::with('user','set')->get();
        return response()->json([
            'comments' => $comments,
        ],200);
    }
}
