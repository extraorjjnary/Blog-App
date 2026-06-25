<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// for public or guest, and list posts and specific post 
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::post('/posts/{post}/reactions', ReactionController::class);


// protected routes
Route::middleware('auth:sanctum')->group(function () {
    // store, update, delete

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
