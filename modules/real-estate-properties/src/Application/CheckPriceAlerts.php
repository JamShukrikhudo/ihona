<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Application;

use Illuminate\Support\Facades\Event;
use Liberu\RealEstate\Properties\Domain\Events\PriceAlertTriggered;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class CheckPriceAlerts
{
    public function handle(int|string|null $teamId = null): int
    {
        $alerts = PropertyPriceAlert::query()
            ->active()
            ->when($teamId !== null, fn ($query) => $query->where('team_id', $teamId))
            ->with('property')
            ->get();
        $triggered = 0;

        foreach ($alerts as $alert) {
            $property = $alert->property;
            $initial = (float) $alert->initial_price;
            $current = (float) $property->price;

            if ($initial <= 0 || $current <= 0) {
                continue;
            }

            $change = (($current - $initial) / $initial) * 100;
            if (abs($change) < (float) $alert->alert_percentage) {
                continue;
            }

            Event::dispatch(new PriceAlertTriggered($alert, $property, round($change, 2)));
            $alert->update(['initial_price' => $current]);
            $triggered++;
        }

        return $triggered;
    }
}
