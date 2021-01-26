<?php

namespace App\Http\Controllers\Image;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function create(Request $request){
        if($request->hasFile('image')){
            $fileName = time().'_'.Str::random(10);
            $path = Storage::putFileAs('post', $request->image,$fileName);
            return \response()->json([
                'message' => 'them anh thanh cong',
                'path' => "https://kaopiz-final.s3-ap-southeast-1.amazonaws.com/".$path,
            ],201);
        }else {
            return response()->json([
                "errors" => "file anh khong dung",
            ],400);
        }
    }
}
