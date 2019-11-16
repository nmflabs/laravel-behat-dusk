<?php

namespace Nmflabs\LaravelBehatDusk;

use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/../config/behat-dusk.php');

        if ($this->app instanceof LaravelApplication) {
            $this->publishes([$source => config_path('behat-dusk.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('behat-dusk');
        }

        $this->mergeConfigFrom($source, 'behat-dusk');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
