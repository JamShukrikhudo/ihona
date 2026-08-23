<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\Branch;

final class UpdateBranch
{
    /** @param array<string, mixed> $attributes */
    public function handle(int|string $teamId, int|string $branchId, array $attributes): Branch
    {
        if (array_key_exists('name', $attributes) && trim((string) $attributes['name']) === '') {
            throw ValidationException::withMessages(['name' => 'A branch name is required.']);
        }
        if (array_key_exists('code', $attributes) && ! preg_match('/^[A-Z0-9-]{2,20}$/', strtoupper(trim((string) $attributes['code'])))) {
            throw ValidationException::withMessages(['code' => 'Branch codes use 2-20 letters, numbers, or hyphens.']);
        }

        return DB::transaction(function () use ($teamId, $branchId, $attributes): Branch {
            $branch = Branch::query()->forTeam($teamId)->findOrFail($branchId);
            if (array_key_exists('code', $attributes)) {
                $attributes['code'] = strtoupper(trim((string) $attributes['code']));
            }
            $branch->fill($attributes);
            $branch->save();

            return $branch->refresh();
        });
    }
}
