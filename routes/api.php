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
    Route::get('/preferences', [PreferenceController::class, 'index'])->name(RouteNames::PREF_SHOW);
    Route::post('/preferences', [PreferenceController::class, 'storeOrUpdate'])->name(RouteNames::PREF_STORE);
    Route::delete('/preferences', [PreferenceController::class, 'destroy'])->name(RouteNames::PREF_DESTROY); //TODO deletable
});
