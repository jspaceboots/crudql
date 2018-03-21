<?php

namespace jspaceboots\crudql;

use Illuminate\Support\ServiceProvider;
use jspaceboots\crudql\Commands\ScaffoldCommand;

class crudqlServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/Config/crudql.php' => config_path('crudql.php')], 'config');
        $this->publishes([__DIR__ . '/public' => public_path('vendor/CrudQL')], 'public');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'CrudQL');

        if ($this->app->runningInConsole()) {
            $this->commands([
                 ScaffoldCommand::class
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register(\jspaceboots\crudql\Providers\CrudServiceProvider::class);
    }
}