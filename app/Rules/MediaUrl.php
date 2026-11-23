<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Accepts an absolute http(s) URL or a site-relative path such as "/storage/media/x.jpg",
 * which is what the media library hands back for files on the local public disk.
 */
class MediaUrl implements ValidationRule
{
    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! ($this->isAbsoluteUrl($value) || $this->isSitePath($value))) {
            $fail('حقل :attribute يجب أن يكون رابطاً صالحاً.');
        }
    }

    private function isAbsoluteUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    private function isSitePath(string $value): bool
    {
        return preg_match('#^/(?!/)[^\s\\\\]*$#', $value) === 1;
    }
}
