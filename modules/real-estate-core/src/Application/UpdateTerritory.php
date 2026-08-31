<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Territory;

final class UpdateTerritory
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $territoryId, array $attributes): Territory
    {
        if (array_key_exists('name', $attributes) && trim((string) $attributes['name']) === '') {
            throw ValidationException::withMessages(['name' => 'A territory name is required.']);
        }
        if (array_key_exists('code', $attributes) && ! preg_match('/^[A-Za-z0-9-]{2,20}$/', (string) $attributes['code'])) {
            throw ValidationException::withMessages(['code' => 'Territory codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(function () use ($teamId, $territoryId, $attributes): Territory {
            $territory = Territory::query()->forTeam($teamId)->findOrFail($territoryId);
            if (array_key_exists('name', $attributes)) {
                $attributes['name'] = trim((string) $attributes['name']);
            }
            if (array_key_exists('code', $attributes)) {
                $attributes['code'] = strtoupper(trim((string) $attributes['code']));
            }
            $territory->fill($attributes)->save();

            return $territory->refresh();
        });
    }
}
