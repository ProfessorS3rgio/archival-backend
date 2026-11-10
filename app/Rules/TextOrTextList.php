<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TextOrTextList implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value)) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $text) {
                if ($text !== null && ! is_string($text)) {
                    $fail('The :attribute must contain text only.');
                    return;
                }
            }
            return;
        }

        $fail('The :attribute must contain text only.');
    }
}
