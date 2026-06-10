<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// for public or guest
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// protected routes
Route::middleware('auth:sanctum')->group(function () {
    // dashboard
    // crud
    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
