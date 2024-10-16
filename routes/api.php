<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => '/user', 'controller' => UserController::class, 'middleware' => ['auth:sanctum']],
    function () {
        Route::get('/', 'show');
        Route::post('/store', 'store');
    }
);


Route::post('/login', AuthenticationController::class);
