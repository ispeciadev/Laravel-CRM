<?php

namespace Ispecia\Voip\Providers;

use Illuminate\Support\ServiceProvider;

class VoipServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
        
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'voip');
        
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'voip');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerCommands();
        
        // Register VoipManager as singleton
        $this->app->singleton(\Ispecia\Voip\Services\VoipManager::class);
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Ispecia\Voip\Console\Commands\GenerateVoipToken::class,
                \Ispecia\Voip\Console\Commands\MigrateVoipConfigCommand::class,
                \Ispecia\Voip\Console\Commands\SetupVoipCommand::class,
            ]);
        }
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/voip.php', 'voip'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}
