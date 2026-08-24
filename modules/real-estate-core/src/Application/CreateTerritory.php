<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Territory;

final class CreateTerritory
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, array $attributes): Territory
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $code = strtoupper(trim((string) ($attributes['code'] ?? '')));
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'A territory name is required.']);
        }
        if (! preg_match('/^[A-Z0-9-]{2,20}$/', $code)) {
            throw ValidationException::withMessages(['code' => 'Territory codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(fn (): Territory => Territory::query()->create([
            'team_id' => $teamId,
            'name' => $name,
            'code' => $code,
            'boundary' => $attributes['boundary'] ?? null,
            'metadata' => $attributes['metadata'] ?? [],
        ]));
    }
}
