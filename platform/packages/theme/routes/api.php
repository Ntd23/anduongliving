<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'api/v1',
    'namespace' => 'Botble\Theme\Http\Controllers\API',
], function (): void {
    Route::get('theme/options/public', 'ThemeOptionController@publicOptions');
});
