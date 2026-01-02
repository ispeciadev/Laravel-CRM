<?php

namespace Ispecia\Installer\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Ispecia\Installer\Helpers\DatabaseManager;

class CanInstall
{
    /**
     * Handles Requests if application is already installed then redirect to dashboard else to installer.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Str::contains($request->getPathInfo(), '/install')) {
            if ($this->isAlreadyInstalled() && ! $request->ajax()) {
                return redirect()->route('admin.dashboard.index');
            }
        } else {
            if (! $this->isAlreadyInstalled()) {
                return redirect()->route('installer.index');
            }
        }

        return $next($request);
    }

    /**
     * Check if application is already installed.
     */
    public function isAlreadyInstalled(): bool
    {
        // Check database first (works on ephemeral filesystems like Railway)
        if (app(DatabaseManager::class)->isInstalled()) {
            // Try to create file marker if it doesn't exist
            if (!file_exists(storage_path('installed'))) {
                @touch(storage_path('installed'));
            }
            
            Event::dispatch('krayin.installed');

            return true;
        }

        // Fallback to file check
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        return false;
    }
}
