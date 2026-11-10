<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return; // The string rule supplies the type error.
        }

        if (str_contains($value, "\0")) {
            $fail('The :attribute contains an unsupported character.');
        } elseif (strlen($value) > 72) {
            $fail('The :attribute must be 72 bytes or fewer. Accented characters and emoji may use more than one byte.');
        }
    }
}
