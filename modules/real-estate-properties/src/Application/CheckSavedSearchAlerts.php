<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\Event;
use Liberu\RealEstate\Properties\Domain\Events\SavedSearchAlertTriggered;
use Liberu\RealEstate\Properties\Domain\PropertyStatus;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertySavedSearch;

/**
 * Scheduled (see routes/console.php): for every saved search, finds
 * properties published since the search's last notification and — if any
 * match — fires SavedSearchAlertTriggered once per search with the whole
 * batch, then advances last_notified_at so the same property is never
 * re-announced on the next run.
 */
final class CheckSavedSearchAlerts
{
    public function handle(int|string|null $teamId = null): int
    {
        $searches = PropertySavedSearch::query()
            ->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))
            ->get();
        $notified = 0;

        foreach ($searches as $search) {
            $since = $search->last_notified_at ?? $search->created_at;
            $criteria = $search->criteria;

            $matches = Property::query()
                ->forTeam($search->team_id)
                ->status(PropertyStatus::Available)
                ->where('published_at', '>', $since)
                ->search($criteria['search'] ?? null)
                ->priceRange($criteria['minPrice'] ?? null, $criteria['maxPrice'] ?? null)
                ->propertyType($criteria['propertyType'] ?? null)
                ->when(
                    ! empty($criteria['selectedAmenities']),
                    fn ($query) => $query->hasAmenities($criteria['selectedAmenities']),
                )
                ->latest('published_at')
                ->get();

            $search->update(['last_notified_at' => now()]);

            if ($matches->isEmpty()) {
                continue;
            }

            Event::dispatch(new SavedSearchAlertTriggered($search, $matches));
            $notified++;
        }

        return $notified;
    }
}
