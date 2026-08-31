<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Domain\Events;

use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;

final class PriceAlertTriggered
{
    public function __construct(
        public readonly PropertyPriceAlert $alert,
        public readonly Property $property,
        public readonly float $percentageChange,
    ) {}
}
