<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalArchiveChat
{
    public static function available(Request $request): bool
    {
        return config('archive-chat.enabled')
            && in_array($request->server('REMOTE_ADDR'), ['127.0.0.1', '::1'], true)
            && in_array($request->getHost(), ['localhost', '127.0.0.1', '[::1]'], true);
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! self::available($request)) {
            return new JsonResponse(['message' => 'Not found.'], 404);
        }

        $origin = $request->header('Origin');
        if ($request->header('Sec-Fetch-Site') === 'cross-site'
            || ($origin !== null && $origin !== $request->getSchemeAndHttpHost())) {
            return new JsonResponse(['message' => 'Cross-origin requests are blocked.'], 403);
        }
        if ($request->isMethod('POST') && ! $request->isJson()) {
            return new JsonResponse(['message' => 'Send a JSON request.'], 415);
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
