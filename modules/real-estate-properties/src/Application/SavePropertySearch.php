<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Models\PropertySavedSearch;

final class SavePropertySearch
{
    /** @param array<string, mixed> $criteria */
    public function handle(int|string $teamId, int|string $userId, string $name, array $criteria): PropertySavedSearch
    {
        $name = trim($name);
        if ($name === '' || $criteria === []) {
            throw ValidationException::withMessages(['saved_search' => 'A saved search needs a name and at least one search criterion.']);
        }

        return PropertySavedSearch::query()->create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'name' => $name,
            'criteria' => $criteria,
        ]);
    }
}
