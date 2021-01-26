<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    public  function index(){
        $user = JWTAuth::parseToken()->authenticate();
        $notifications= $user->notifications;
        return response()->json([
            'notifications' => array_column($notifications->toArray(),'data'),
            'unread' => count($user->unreadNotifications)
        ],200);
    }

    public function update(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = Notification::query()->find($request->id);
        if($notifications){
            if($notifications->notifiable_id == $user->id){
                $notifications->read_at = now();
                $notifications->save();
                return response()->json([
                    'message' => 'done'
                ],201);
            }
            else return  response()->json([
                'errors' => 'can do this action'
            ],403);
        }
        else return  response()->json([
            'errors' => 'not found'
        ],404);
    }

    public function updateAll(){
        $user = JWTAuth::parseToken()->authenticate();
        $user->unreadNotifications->markAsRead();
        return response()->json([
            'message' => 'done'
        ],201);
    }
}
