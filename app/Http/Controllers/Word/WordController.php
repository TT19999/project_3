<?php

namespace App\Http\Controllers\Word;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

$api_app_id = '1a9d59e9';
$api_app_key='6cc8009d5234b2cc349a8b7013f36a47';
$Entries_link ='https://od-api.oxforddictionaries.com/api/v2/entries/en-gb/';

class WordController extends Controller
{
    public function index(){

    }

    public function show(Request $request){
        $word = $request->input('word');
        $responseJson = Http::withHeaders([
            'app_key' => '6cc8009d5234b2cc349a8b7013f36a47',
            'app_id' => '1a9d59e9'
        ])->get('https://od-api.oxforddictionaries.com/api/v2/entries/en/'.$word);
        // $response2= \json_decode(\json_encode($responseJson->body()),true);
        $response = json_decode($responseJson->body());
//        if($responseJson->status() == 404) {
//            return response()->json([
//                'error' => "not found",
//            ],404);
//        }
        $word= \json_decode(\json_encode($response),true);
        return \response()->json(\compact('word'));
    }
}
