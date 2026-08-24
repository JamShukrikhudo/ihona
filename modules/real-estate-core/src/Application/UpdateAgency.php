<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Agency;

final class UpdateAgency
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $agencyId, array $attributes): Agency
    {
        if (array_key_exists('name', $attributes) && trim((string) $attributes['name']) === '') {
            throw ValidationException::withMessages(['name' => 'An agency name is required.']);
        }
        if (array_key_exists('code', $attributes) && ! preg_match('/^[A-Za-z0-9-]{2,20}$/', (string) $attributes['code'])) {
            throw ValidationException::withMessages(['code' => 'Agency codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(function () use ($teamId, $agencyId, $attributes): Agency {
            $agency = Agency::query()->forTeam($teamId)->findOrFail($agencyId);
            if (array_key_exists('name', $attributes)) {
                $attributes['name'] = trim((string) $attributes['name']);
            }
            if (array_key_exists('code', $attributes)) {
                $attributes['code'] = strtoupper(trim((string) $attributes['code']));
            }
            $agency->fill($attributes)->save();

            return $agency->refresh();
        });
    }
}
