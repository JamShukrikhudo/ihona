<?php

namespace App\Services;

use InvalidArgumentException;

class CountryConfiguration
{
    public function all(): array
    {
        return config('countries', []);
    }

    public function get(string $countryCode): array
    {
        $code = strtoupper($countryCode);
        $country = config("countries.$code");

        if (! is_array($country)) {
            throw new InvalidArgumentException("Unsupported operating country [$code].");
        }

        return ['code' => $code, ...$country];
    }

    public function defaults(string $countryCode): array
    {
        $country = $this->get($countryCode);

        return array_intersect_key($country, array_flip([
            'currency', 'locale', 'language', 'timezone', 'date_format',
            'measurement_system', 'area_unit',
        ]));
    }
}
