<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Terminology;

final class SetTerminology
{
    public function handle(int|string $teamId, string $key, string $value, string $locale = 'en'): Terminology
    {
        if (! preg_match('/^[a-zA-Z0-9_.-]{1,100}$/', $key)) {
            throw ValidationException::withMessages(['key' => 'A valid terminology key is required.']);
        }
        if (trim($value) === '') {
            throw ValidationException::withMessages(['value' => 'A terminology value is required.']);
        }
        if (! preg_match('/^[a-zA-Z]{2,3}(?:[-_][a-zA-Z]{2,4})?$/', $locale)) {
            throw ValidationException::withMessages(['locale' => 'A valid locale is required.']);
        }

        return Terminology::query()->updateOrCreate(['team_id' => $teamId, 'locale' => $locale, 'key' => $key], ['value' => trim($value)]);
    }
}
