<?php

use App\Constants\RouteNames;
use App\Http\Controllers\ArticleController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index'])->name(RouteNames::ARTICLE_INDEX);   //with pagination and filters
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name(RouteNames::ARTICLE_SHOW);
    Route::post('/articles', [ArticleController::class, 'store'])->name(RouteNames::ARTICLE_STORE);
    Route::put('/articles/{id}', [ArticleController::class, 'update'])->name(RouteNames::ARTICLE_UPDATE);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name(RouteNames::ARTICLE_DESTROY);

    Route::get('/update-articles', [ArticleController::class, 'updateArticles'])->name(RouteNames::ARTICLE_SCHEDULAR);
    Route::get('/personalized-feed', [ArticleController::class, 'personalizedFeed'])->name(RouteNames::ARTICLE_FEED);
});

/*
|--------------------------------------------------------------------------
| User Preferences Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/preferences', [PreferenceController::class, 'index'])->name(RouteNames::PREF_SHOW);
    Route::post('/preferences', [PreferenceController::class, 'store'])->name(RouteNames::PREF_STORE);
});
