<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('document-api', function (Request $request) {
            $user = $request->user();
            $key = $user ? get_class($user).':'.$user->getAuthIdentifier() : 'ip:'.$request->ip();

            return Limit::perMinute(120)->by($key);
        });
    }
}
