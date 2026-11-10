<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotLegislatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (strtolower((string) (Auth::user()->role ?? '')) === 'legislator') {
            return redirect()->route('dashboard')
                ->with('error', 'Legislators are not authorized to add officials.');
        }

        return $next($request);
    }
}
