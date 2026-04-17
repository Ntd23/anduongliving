<?php

use Illuminate\Support\Facades\Route;
use Anduongliving\MainMenu\Http\Controllers\Api\MenuController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api',
    'namespace' => 'Anduongliving\MainMenu\Http\Controllers\Api',
], function (): void {
    Route::get('menus/{slug}', [MenuController::class, 'show']);
});