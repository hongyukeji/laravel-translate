<?php

namespace Hongyukeji\LaravelTranslate;

use Themsaid\Langman\Manager;
use Illuminate\Support\ServiceProvider;
use Hongyukeji\LaravelTranslate\Commands\AllCommand;
use Hongyukeji\LaravelTranslate\Commands\MissingCommand;
use Hongyukeji\LaravelTranslate\Translators\TranslatorInterface;

class TranslateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            /*$this->publishes([
                __DIR__.'/../config/config.php' => config_path('translate.php'),
            ], 'config');*/
            $this->publishes([
                __DIR__ . '/../config/translate.php' => config_path('translate.php'),
            ]);
            // Registering package commands.
            $this->commands([
                AllCommand::class,
                MissingCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'translate');

        $this->app->bind(TranslatorInterface::class, config('translate.translator'));

        // Register the main class to use with the facade
        $this->app->singleton('translate', function () {
            config([
                'langman.path' => config('translate.path'),
            ]);

            return new Translate(app(Manager::class), app(TranslatorInterface::class));
        });
    }
}
