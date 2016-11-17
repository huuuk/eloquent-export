<?php
namespace AdvancedEloquent\Export;

use Illuminate\Support\ServiceProvider;

class ExportServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish package config
        $this->publishes([
            __DIR__.'/../config/export.php' => config_path('export.php')
        ], 'config');

        // publish package views
        $this->publishes([
            __DIR__.'/../resources/views/' => base_path('resources/views/vendor/eloquent-export'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../resources/lang/ru' => base_path('resources/lang/packages/ru/eloquent-export'),
        ], 'lang');

        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'eloquent-export');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'eloquent-export');

        if (config('export.builtin_routes')) {
            include 'routes.php';
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/export.php', 'export');
    }

}
