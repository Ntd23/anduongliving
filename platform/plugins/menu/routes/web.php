<?php

use Botble\Base\Facades\AdminHelper;
use Menu\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'main-menus', 'as' => 'main-menu.'], function () {
        Route::resource('', MenuController::class)->parameters(['' => 'menu']);
    });
});
