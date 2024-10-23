<?php

use App\Constants\RouteNames;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PreferenceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/register', [AuthController::class, 'register'])->name(RouteNames::AUTH_REGISTER);
Route::post('/login', [AuthController::class, 'login'])->name(RouteNames::AUTH_LOGIN);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name(RouteNames::AUTH_LOGOUT);
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name(RouteNames::PASSWORD_RESET);

/*
|--------------------------------------------------------------------------
| Article Management Routes
|--------------------------------------------------------------------------
|
*/

/*
|--------------------------------------------------------------------------
| User Preferences Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/preferences', [PreferenceController::class, 'show'])->name(RouteNames::PREF_SHOW);
    Route::post('/preferences', [PreferenceController::class, 'store'])->name(RouteNames::PREF_STORE);
});
