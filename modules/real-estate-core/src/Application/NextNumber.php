<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\NumberSequence;

final class NextNumber
{
    public function handle(int|string $teamId, string $key, ?string $prefix = null, int $padding = 6): string
    {
        $key = trim($key);
        if ($key === '' || ! preg_match('/^[a-zA-Z0-9_.-]{1,80}$/', $key)) {
            throw ValidationException::withMessages(['key' => 'A valid numbering key is required.']);
        }
        if ($padding < 1 || $padding > 20) {
            throw ValidationException::withMessages(['padding' => 'Padding must be between 1 and 20.']);
        }
        if ($prefix !== null && strlen($prefix) > 30) {
            throw ValidationException::withMessages(['prefix' => 'The numbering prefix cannot exceed 30 characters.']);
        }

        return DB::transaction(function () use ($teamId, $key, $prefix, $padding): string {
            $sequence = NumberSequence::query()->forTeam($teamId)->where('key', $key)->lockForUpdate()->first();
            if ($sequence === null) {
                $sequence = NumberSequence::query()->create(['team_id' => $teamId, 'key' => $key, 'prefix' => $prefix, 'next_value' => 1, 'padding' => $padding]);
            }
            $value = (int) $sequence->next_value;
            $sequence->increment('next_value');

            return ($sequence->prefix ?? $prefix ?? '').str_pad((string) $value, (int) $sequence->padding, '0', STR_PAD_LEFT);
        });
    }
}
