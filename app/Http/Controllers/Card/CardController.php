<?php

namespace App\Http\Controllers\Card;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Set;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CardController extends Controller
{
    public function create(Request $request){
        $set=Set::query()->find($request->set_id);
        $card = $set->cards()->create(
            $request->all(),
        );
        return response()->json([
            'card' => $card,
        ],201);
    }

    public function edit(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $set=Set::query()->find($request->set_id);
        if($user->can('update', $set)){
            $card = Card::query()->findOrFail($request->card_id);
            $card->update($request->all());
            return response()->json([
                'card' => $card,
            ],200);
        }
    }

    public function delete(Request $request){
        $user = JWTAuth::parseToken() ->authenticate();
        $set=Set::query()->find($request->set_id);
        if($user->can('update', $set)){
            $card = Card::query()->findOrFail($request->card_id);
            $card->delete();
            return response()->json([
                'message' => 'done',
            ],200);
        }
        else {
            return  response()->json([
                'error' => 'khong the xoa'
            ],403);
        }
    }
}
