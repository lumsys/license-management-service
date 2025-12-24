<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and assigned
| the "api" middleware group. They are prefixed with /api.
|
*/

Route::prefix('v1')->group(function () {
    require base_path('routes/api_v1.php');
});
