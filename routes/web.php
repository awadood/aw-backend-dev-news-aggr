<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return redirect('/swagger-ui');
});
