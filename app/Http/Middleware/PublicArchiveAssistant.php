<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Allows only the explicitly enabled, read-only public assistant endpoint. */
class PublicArchiveAssistant
{
    public function handle(Request $request, Closure $next): Response
    {
        $hosts = config('archive-chat.public_hosts', []);
        $host = strtolower($request->getHost());

        if (! config('archive-chat.public_enabled') || ! in_array($host, $hosts, true)) {
            return new JsonResponse(['message' => 'Not found.'], 404);
        }

        if (! $request->isSecure() && ! in_array($host, ['localhost', '127.0.0.1', '[::1]'], true)) {
            return new JsonResponse(['message' => 'HTTPS is required.'], 403);
        }

        if (! $request->isMethod('POST') || ! $request->isJson()) {
            return new JsonResponse(['message' => 'Send a JSON POST request.'], 415);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
