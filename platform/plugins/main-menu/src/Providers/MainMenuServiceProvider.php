<?php

namespace Anduongliving\MainMenu\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class MainMenuServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/main-menu')
            ->loadHelpers()
            ->loadRoutes(['web', 'api']);
            
            
    }
}
