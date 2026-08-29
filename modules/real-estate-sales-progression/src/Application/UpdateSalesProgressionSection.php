<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionSection;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

final class UpdateSalesProgressionSection
{
    /** @param array<string, mixed> $value */
    public function handle(SalesProgression $progression, int|string $teamId, SalesProgressionSection $section, array $value): SalesProgression
    {
        if ((string) $progression->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['progression' => 'The progression does not belong to this team.']);
        }
        $progression->forceFill([$section->value => $value])->save();

        return $progression->refresh();
    }
}
