<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Branch;

final class CreateBranch
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, array $attributes): Branch
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $code = strtoupper(trim((string) ($attributes['code'] ?? '')));
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'A branch name is required.']);
        }
        if (! preg_match('/^[A-Z0-9-]{2,20}$/', $code)) {
            throw ValidationException::withMessages(['code' => 'Branch codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(fn (): Branch => Branch::query()->create([
            'team_id' => $teamId,
            'name' => $name,
            'code' => $code,
            'address' => $attributes['address'] ?? null,
            'phone' => $attributes['phone'] ?? null,
            'email' => $attributes['email'] ?? null,
            'metadata' => $attributes['metadata'] ?? [],
        ]));
    }
}
