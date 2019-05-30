<?php

namespace Datomon\LaravelNewebpay\Providers;

use Illuminate\Support\ServiceProvider;
use Datomon\LaravelNewebpay\Console\Commands\Init;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // route
        $this->loadRoutesFrom(__DIR__.'/../../routes/route.php');

        // migration
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // blade
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'newebpay');

        //add artisan command
        if ($this->app->runningInConsole()) {
            $this->commands([
                Init::class,
            ]);
        }
    }

    public function register()
    {

    }
}