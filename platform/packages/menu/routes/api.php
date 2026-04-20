<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/v1',
    'namespace' => 'Botble\Menu\Http\Controllers\API',
], function (): void {
    Route::get('menus/main-menu', 'MenuController@mainMenu');
    Route::get('menus/location/{location}', 'MenuController@byLocation');
    Route::get('menus/{menu}', 'MenuController@show');
});
