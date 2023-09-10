<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Blog\CommentController;
use App\Http\Controllers\Blog\LikeController;
use App\Http\Controllers\Blog\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


// protected auth
Route::group(['middleware' => ['auth:sanctum']], function () {

    // logout
    Route::post('logout', [AuthController::class, 'logout']);

    // userlogin
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user', [AuthController::class, 'update']);


    // post
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::put('posts/{id}', [PostController::class, 'update']);
    Route::delete('posts/{id}', [PostController::class, 'destroy']);

    // comment
    Route::get('posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('posts/{id}/comments', [CommentController::class, 'store']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // like
    Route::post('posts/{id}/likes', [LikeController::class, 'likeOrUnlike']);
});
