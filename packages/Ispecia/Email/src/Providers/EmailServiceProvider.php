<?php

namespace Ispecia\Email\Providers;

use Illuminate\Support\ServiceProvider;
use Ispecia\Email\Console\Commands\ProcessInboundEmails;
use Ispecia\Email\Console\Commands\SendScheduledEmails;
use Ispecia\Email\InboundEmailProcessor\Contracts\InboundEmailProcessor;
use Ispecia\Email\InboundEmailProcessor\SendgridEmailProcessor;
use Ispecia\Email\InboundEmailProcessor\WebklexImapEmailProcessor;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        
        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->app->bind(InboundEmailProcessor::class, function ($app) {
            $driver = config('mail-receiver.default');

            if ($driver === 'sendgrid') {
                return $app->make(SendgridEmailProcessor::class);
            }

            if ($driver === 'webklex-imap') {
                return $app->make(WebklexImapEmailProcessor::class);
            }

            throw new \Exception("Unsupported mail receiver driver [{$driver}].");
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register the console commands of this package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ProcessInboundEmails::class,
                SendScheduledEmails::class,
            ]);
        }
    }
}
