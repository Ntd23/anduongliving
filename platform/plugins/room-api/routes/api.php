<?php

use Botble\RoomApi\Http\Controllers\API\RoomApiController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/v1/test-rooms',
    'namespace' => 'Botble\RoomApi\Http\Controllers\API',
], function (): void {
    Route::get('/', [RoomApiController::class, 'index']);
});
