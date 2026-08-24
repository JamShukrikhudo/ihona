<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Agency;

final class CreateAgency
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, array $attributes): Agency
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $code = strtoupper(trim((string) ($attributes['code'] ?? '')));
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'An agency name is required.']);
        }
        if (! preg_match('/^[A-Z0-9-]{2,20}$/', $code)) {
            throw ValidationException::withMessages(['code' => 'Agency codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(fn (): Agency => Agency::query()->create([
            'team_id' => $teamId,
            'name' => $name,
            'code' => $code,
            'active' => $attributes['active'] ?? true,
            'metadata' => $attributes['metadata'] ?? [],
        ]));
    }
}
