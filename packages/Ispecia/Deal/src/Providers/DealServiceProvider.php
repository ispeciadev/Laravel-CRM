<?php

namespace Ispecia\Deal\Providers;

use Illuminate\Support\ServiceProvider;
use Ispecia\Deal\Observers\LeadObserver;
use Ispecia\Lead\Models\Lead;

class DealServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge menu and ACL configurations into Admin config stacks
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/menu.php', 'menu.admin');
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/acl.php', 'acl');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Database, Routes & Views
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'deal');

        // Register the observer for auto-converting leads to deals
        Lead::observe(LeadObserver::class);

        $this->app->register(EventServiceProvider::class);
    }
}
