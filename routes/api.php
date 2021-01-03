<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[AuthController::class, 'login'])->middleware('auth.verify'); //use
Route::get('/verify',[AuthController::class,'verify'])->name("verify")->middleware('auth.signer');;
Route::post('/register', [AuthController::class, 'register']); //use

Route::group(['middleware' => 'auth.jwt'], function () {

    //resser password
    Route::post('/user/resetPassword', [AuthController::class,'resetPassword']);//use

    //profile
    Route::post('/user/profile', [ProfileController::class,'update']);//use
    Route::get('/user/profile',[ProfileController::class,'index']);//use
    Route::post('/user/profile/avatar', [ProfileController::class,'updateAvatar']);//use
    Route::get('/user/profile/show',[ProfileController::class,'show']);//use
});