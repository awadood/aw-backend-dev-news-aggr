<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', function () {
    return redirect('/api/documentation');
});
