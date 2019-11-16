<?php

namespace Nmflabs\LaravelBehatDusk;

use Nmflabs\LaravelBehatDusk\Console;
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

        $this->publishes([$source => config_path('behat-dusk.php')]);

        $this->mergeConfigFrom($source, 'behat-dusk');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\BehatCommand::class,
                Console\InstallCommand::class,
            ]);
        }
    }
}
