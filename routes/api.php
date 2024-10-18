<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => '/user', 'controller' => UserController::class, 'middleware' => ['auth:sanctum']],
    function () {
        Route::get('/', 'show');
        Route::post('/', 'store');
    }
);

Route::group(
    ['prefix' => '/transfer', 'controller' => TransferController::class, 'middleware' => ['auth:sanctum']],
    function () {
        Route::get('/{id}', 'show');
        Route::get('/', 'index');
        Route::post('/', 'store');
    }
);

Route::post('/login', AuthenticationController::class);
