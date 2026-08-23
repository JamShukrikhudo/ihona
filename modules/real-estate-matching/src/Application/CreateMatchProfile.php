<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Matching\Models\MatchProfile;

final class CreateMatchProfile
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): MatchProfile
    {
        $subject = trim((string) ($attributes['subject'] ?? ''));
        if ($subject === '') {
            throw ValidationException::withMessages(['subject' => 'A matching profile subject is required.']);
        }$score = max(0, min(100, (int) ($attributes['score'] ?? 0)));

        return DB::transaction(fn (): MatchProfile => MatchProfile::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'party_id' => $attributes['party_id'] ?? null, 'subject' => $subject, 'score' => $score, 'requirements' => $attributes['requirements'] ?? [], 'affordability' => $attributes['affordability'] ?? [], 'preferences' => $attributes['preferences'] ?? [], 'alerts' => $attributes['alerts'] ?? [], 'feedback' => $attributes['feedback'] ?? [], 'exclusions' => $attributes['exclusions'] ?? []]));
    }
}
