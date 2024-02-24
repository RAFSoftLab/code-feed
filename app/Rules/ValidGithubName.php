<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGithubName implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the string contains only ASCII letters, digits, '-', '_', and '.'
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $value)) {
             $fail('The :attribute may only contain letters, numbers, dashes, and underscores.');
        }
    }
}
