<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
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

// Authentication
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('me', [AuthController::class, 'me']);
});

// POST MY USER
Route::get('post/mypost', [PostController::class, 'mypost']);

// ACCOUNT USER
Route::post('user/profile', [PostController::class, 'updateprofile']);
Route::get('user-all', [PostController::class, 'userall']);
Route::post('user-comment', [PostController::class, 'usercomment']);
Route::delete('user-comment/{id}/delete', [PostController::class, 'deletecomment']);


// CRUD POST 
Route::get('post', [PostController::class, 'index']);
Route::post('post', [PostController::class, 'store']);
Route::get('post/{id}', [PostController::class, 'show']);
Route::post('post/{id}/update', [PostController::class, 'update']);
Route::delete('post/{id}', [PostController::class, 'destroy']);
