<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictStaffAccess
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->role === 'staff') {
            abort_if(
                ($request->is('settings', 'settings/*') && ! $request->is('settings/change-password', 'settings/auto-fill'))
                || $request->is('system-logs', 'system-logs/*', 'log-viewer', 'log-viewer/*', 'sp-*', 'official-profile-pictures/*', 'debug-user'),
                403,
                'Staff can access their own records and account settings only.'
            );
            // A submitted creator ID must never change staff record ownership.
            if (! $request->isMethodSafe()) {
                foreach (['ordinances', 'resolutions', 'draft-ordinances', 'draft-resolutions'] as $resource) {
                    if ($request->is($resource, "$resource/*", "api/$resource", "api/$resource/*")) {
                        $request->merge(['added_by' => $request->user()->id]);
                        break;
                    }
                }
            }
        }
        return $next($request);
    }
}
