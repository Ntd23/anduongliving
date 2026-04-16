<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/v1/widgets', 
    'namespace' => 'Botble\Widget\Http\Controllers\API'], function () {
    Route::get('', 'WidgetController@index');
    Route::get('sidebar/{sidebarId}', 'WidgetController@getBySidebar');
});