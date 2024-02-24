<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGithubURL implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern ='~github\.com/([^/]+)/([^/.]+?)(?:\.git)?$~';
        preg_match($pattern, $value, $matches);
        if (count($matches) < 3) {
            $fail('The :attribute must be a valid GitHub https url.');
            return;
        }
        // Validate with ValidGithubName
        $validGithubName = new ValidGithubName();
        $validGithubName->validate('Organization', $matches[1], fn($message) => $fail($message));
        $validGithubName->validate('Repository', $matches[2], fn($message) => $fail($message));
    }
}
