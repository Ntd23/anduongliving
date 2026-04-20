<?php

namespace Menu\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class MenuServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/menu')
            ->loadHelpers()
            ->loadRoutes(['web', 'api']);
    }
}
