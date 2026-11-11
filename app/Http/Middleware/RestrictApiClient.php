<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;

class RestrictApiClient
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() instanceof ApiClient) {
            $type = strtolower((string) $request->segment(4));
            abort_if(in_array($type, ['draftresolution', 'draftordinance', 'draft-resolutions', 'draft-ordinances'], true), 404);
        }

        return $next($request);
    }
}
