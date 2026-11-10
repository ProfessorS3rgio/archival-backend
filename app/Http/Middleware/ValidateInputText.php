<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateInputText
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethodSafe()) {
            $errors = [];
            $inspect = function (array $values, string $prefix = '') use (&$inspect, &$errors): void {
                foreach ($values as $key => $value) {
                    $field = $prefix.$key;
                    if (is_array($value)) {
                        $inspect($value, $field.'.');
                    } elseif (is_string($value) && (! mb_check_encoding($value, 'UTF-8') || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value))) {
                        $errors[$field] = 'This field contains an unsupported character. Remove control characters and try again.';
                    }
                }
            };
            $inspect($request->input());
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

        return $next($request);
    }
}
