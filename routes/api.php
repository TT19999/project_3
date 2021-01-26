<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Set\SetController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Follow\FollowController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\User\UserController;

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


    //set
    Route::get('/set',[SetController::class,'index']);
    Route::post('/set/create',[SetController::class,'create']);
    Route::delete('/set/delete',[SetController::class,'delete']);
    Route::post('/set/update',[SetController::class,'update']);

    //COmment
    Route::get('/set/comment',[CommentController::class,'index']);
    Route::post('/set/{id}/comment',[CommentController::class,'create']);//use
    Route::post('/comment/edit',[CommentController::class,'update']);//use
    Route::delete('/comment/delete',[CommentController::class,'delete']);//use
    Route::get('/comment/all',[CommentController::class,'all']);

    //card
    Route::post('/card/edit',[\App\Http\Controllers\Card\CardController::class,'edit']);
    Route::delete('/card/delete',[\App\Http\Controllers\Card\CardController::class,'delete']);

    //follower
    Route::post('/follow',[FollowController::class,'create']);
    Route::delete('/follow',[FollowController::class,'delete']);

    //notification
    Route::get('/notifications',[NotificationController::class,'index']);
    Route::post('/notifications/update',[NotificationController::class,'update']);
    Route::post('/notifications/update/all',[NotificationController::class,'updateAll']);


    //admin
    Route::group(['middleware' => 'auth.admin'], function () {
        Route::get('/admin/user', [UserController::class, 'index']);//use
        Route::get('/admin/user/show',[UserController::class,'show']);//use
        Route::delete('admin/user/delete',[UserController::class,'delete']);
        Route::post('/admin/user/restore',[UserController::class,'restore']);

    });
});
    Route::get('/search',[\App\Http\Controllers\Search\SearchController::class,'show']);
    Route::get('/set/show',[SetController::class,'show']);
    Route::get('/word/show',[\App\Http\Controllers\Word\WordController::class,'show']);
    Route::post('/image/create',[\App\Http\Controllers\Image\ImageController::class,'create']);
    Route::post('/card/create',[\App\Http\Controllers\Card\CardController::class,'create']);
