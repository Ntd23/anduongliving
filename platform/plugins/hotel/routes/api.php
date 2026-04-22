<?php

use Botble\Hotel\Http\Controllers\API\CustomerAuthController;
use Botble\Hotel\Http\Controllers\API\CustomerProfileController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/v1/customer',
    'middleware' => ['api'],
], function (): void {
    Route::post('register', [CustomerAuthController::class, 'register']);
    Route::post('login', [CustomerAuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function (): void {
        Route::get('logout', [CustomerAuthController::class, 'logout']);
        Route::get('me', [CustomerProfileController::class, 'me']);
    });
});
