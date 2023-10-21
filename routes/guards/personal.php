<?php
use \App\Http\Controllers\Api\PersonalAuthController;

Route::prefix('personal')->group(function () {
    Route::prefix('auth')->group(function (){
        Route::post('login', [PersonalAuthController::class, 'login']);

    });

    Route::middleware('auth:personal')->group(function () {
        Route::get('user', [PersonalAuthController::class, 'user']);
        Route::post('logout', [PersonalAuthController::class, 'logout']);
    });
});
