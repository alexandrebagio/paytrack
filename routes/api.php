<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    ['prefix' => '/user', 'controller' => UserController::class, 'middleware' => ['auth:sanctum']],
    function () {
        Route::get('/', 'show');
    }
);

Route::get('/user/confirmation/{rememberToken}', [UserController::class, 'confirmation']); // TODO passar a rota para front

Route::group(
    ['prefix' => '/transfer', 'controller' => TransferController::class, 'middleware' => ['auth:sanctum']],
    function () {
        Route::get('/{id}', 'show');
        Route::get('/', 'index');
        Route::post('/', 'store');
    }
);

Route::post('/login', AuthenticationController::class)->middleware('guest');
Route::post('/user', [UserController::class, 'store'])->middleware('guest');
