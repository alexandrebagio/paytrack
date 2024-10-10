<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/user'], function () {
    Route::get('/', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/authentication', [AuthenticationController::class, 'login']);
});
