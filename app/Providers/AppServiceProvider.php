<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionSyncService;
use Illuminate\Support\Facades\Artisan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-sync permissions when running in development or when explicitly enabled
        // This can be disabled in production for performance
        if (config('app.auto_sync_permissions', false)) {
            // Sync permissions on application boot (development only)
            if (app()->environment('local', 'development')) {
                // Only sync if permissions table exists
                try {
                    $syncService = app(PermissionSyncService::class);
                    $syncService->syncFromRoutes();
                } catch (\Exception $e) {
                    // Silently fail if database is not ready
                }
            }
        }
    }
}
