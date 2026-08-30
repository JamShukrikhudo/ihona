<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyFavorite;

final class TogglePropertyFavorite
{
    public function handle(int|string $teamId, int|string $userId, int|string $propertyId): bool
    {
        $property = Property::query()->forTeam($teamId)->findOrFail($propertyId);
        $favorite = PropertyFavorite::query()
            ->where('team_id', $teamId)
            ->where('user_id', $userId)
            ->where('property_id', $property->getKey())
            ->first();

        if ($favorite !== null) {
            $favorite->delete();

            return false;
        }

        DB::transaction(fn () => PropertyFavorite::query()->create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'property_id' => $property->getKey(),
        ]));

        return true;
    }
}
