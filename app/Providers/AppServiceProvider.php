<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        \App\Support\StaffDocumentAccess::registerScopes();

        // Opcodes Log Viewer requires an explicit production authorization
        // gate. Keep this aligned with the middleware protecting its routes.
        Gate::define('viewLogViewer', function ($user): bool {
            return strtolower((string) ($user->role ?? '')) === 'admin';
        });

        RateLimiter::for('document-api', function (Request $request) {
            $user = $request->user();
            $key = $user ? get_class($user).':'.$user->getAuthIdentifier() : 'ip:'.$request->ip();

            return Limit::perMinute(120)->by($key);
        });

        RateLimiter::for('archive-assistant', function (Request $request) {
            return Limit::perMinute((int) config('archive-chat.public_rate_limit', 30))
                ->by('archive-assistant:'.$request->ip());
        });
    }
}
