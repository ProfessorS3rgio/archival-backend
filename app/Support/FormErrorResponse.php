<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormErrorResponse
{
    public static function respond(Response $response, Request $request): Response
    {
        $status = $response->getStatusCode();
        $message = match (true) {
            $status === 419 => 'Your session expired. Refresh the page, sign in if needed, and try again.',
            $status === 413 => 'The upload is too large. Choose fewer or smaller files and try again.',
            $status === 429 => 'Too many attempts. Please wait a moment before trying again.',
            $status === 403 => 'You do not have permission to perform this action.',
            $status === 404 => 'This record is no longer available. Refresh the page before trying again.',
            $status >= 500 => 'We could not complete your request. Check whether your changes were saved before trying again. If this continues, contact the administrator.',
            default => null,
        };

        if ($message === null) {
            return $response; // Preserve Laravel's field validation responses.
        }

        // Inertia requests use redirects and error bags; uploads/API calls use JSON.
        if (! $request->header('X-Inertia') && $request->expectsJson()) {
            return response()->json(['message' => $message, 'errors' => ['general' => [$message]]], $status);
        }

        if (! $request->isMethodSafe() && $request->hasSession()) {
            return back(303)->withErrors(['general' => $message]);
        }

        if ($status >= 500 && ! app()->environment('local', 'testing')) {
            return response()->view('errors.request', ['message' => $message], $status);
        }

        return $response;
    }
}
