<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\ServiceProvider;

class KeywordAnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
         $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-keyword-analytics');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            // Publishing the configuration file.
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('keyword-analytics.php'),
            ], 'config');

            // Publishing the translation files.
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/keyword-analytics'),
            ], 'lang');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-keyword-analytics'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-keyword-analytics'),
            ], 'assets');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'keyword-analytics');

        // Register the main class to use with the facade
        $this->app->singleton('keyword-analytics', function () {
            return new Analysis();
        });
    }
}
