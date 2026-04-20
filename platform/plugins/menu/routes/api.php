<?php

use Illuminate\Support\Facades\Route;
use Menu\Http\Controllers\Api\MenuController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api',
    'namespace' => 'Menu\Http\Controllers\Api',
], function (): void {
    Route::get('menus/{location}', [MenuController::class, 'show']);
});