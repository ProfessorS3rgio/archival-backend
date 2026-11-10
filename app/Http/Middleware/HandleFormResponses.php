<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleFormResponses
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethodSafe() || $request->header('X-Inertia') || ! $request->expectsJson() || ! $response instanceof RedirectResponse || ! $request->hasSession()) {
            return $response;
        }

        // Axios follows redirects automatically. Return explicit results so a failed
        // upload redirected back to its form can never be mistaken for a saved file.
        $session = $request->session();
        $flashed = $session->get('_flash.new', []);
        if (in_array('errors', $flashed, true)) {
            $errors = $session->get('errors')->getBag('default')->messages();
            $session->forget('errors');
            return response()->json(['message' => 'Please check your submission.', 'errors' => $errors], 422);
        }
        if (in_array('error', $flashed, true)) {
            $message = $session->get('error');
            $session->forget('error');
            return response()->json(['message' => $message, 'errors' => ['general' => [$message]]], 422);
        }
        if (in_array('success', $flashed, true)) {
            return response()->json(['success' => true, 'message' => $session->get('success')]);
        }

        return $response;
    }
}
