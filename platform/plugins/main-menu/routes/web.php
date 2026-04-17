<?php

use Botble\Base\Facades\AdminHelper;
use Anduongliving\MainMenu\Http\Controllers\MainMenuController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function () {
    Route::group(['prefix' => 'main-menus', 'as' => 'main-menu.'], function () {
        Route::resource('', MainMenuController::class)->parameters(['' => 'main-menu']);
    });
});
