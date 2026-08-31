<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Matching\Domain\MatchProfileSection;
use Liberu\RealEstate\Matching\Models\MatchProfile;

final class UpdateMatchProfileSection
{
    /** @param array<string, mixed> $value */
    public function handle(MatchProfile $profile, int|string $teamId, MatchProfileSection $section, array $value): MatchProfile
    {
        if ((string) $profile->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['profile' => 'The matching profile does not belong to this team.']);
        }

        if ($section === MatchProfileSection::Scoring) {
            $score = $value['score'] ?? null;
            if (! is_int($score) && ! is_numeric($score)) {
                throw ValidationException::withMessages(['score' => 'A numeric score is required.']);
            }
            $score = (int) $score;
            if ($score < 0 || $score > 100) {
                throw ValidationException::withMessages(['score' => 'The score must be between 0 and 100.']);
            }

            return DB::transaction(function () use ($profile, $score): MatchProfile {
                $profile->forceFill(['score' => $score])->save();

                return $profile->refresh();
            });
        }

        return DB::transaction(function () use ($profile, $section, $value): MatchProfile {
            $profile->forceFill([$section->value => $value])->save();

            return $profile->refresh();
        });
    }
}
