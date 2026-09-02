<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Liberu\RealEstate\Properties\Models\Property;

final class PropertyMap extends Component
{
    public Collection $properties;

    public function __construct(?iterable $properties = null)
    {
        $this->properties = self::points($properties ?? self::defaults());
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function points(iterable $properties): Collection
    {
        return Collection::wrap($properties)
            ->map(static function (mixed $property): array {
                $get = static fn (string $key): mixed => is_array($property)
                    ? ($property[$key] ?? null)
                    : ($property->{$key} ?? null);

                return [
                    'id' => $get('id'),
                    'title' => $get('title') ?: $get('address'),
                    'price' => $get('price'),
                    'currency' => self::currencyPrefix($get('currency')),
                    'latitude' => $get('latitude'),
                    'longitude' => $get('longitude'),
                ];
            })
            ->values();
    }

    public static function mappable(?int $teamId = null): Builder
    {
        $teamId ??= auth()->user()?->current_team_id;

        return Property::query()
            ->forTeam($teamId ?? 0)
            ->select(['id', 'title', 'address', 'price', 'currency', 'latitude', 'longitude'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->limit(500);
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-map', ['properties' => $this->properties]);
    }

    private static function defaults(): Collection
    {
        $key = self::class.':defaults';

        if (! app()->bound($key)) {
            app()->instance($key, self::mappable()->get());
        }

        return app($key);
    }

    private static function currencyPrefix(mixed $currency): ?string
    {
        return match (strtoupper((string) $currency)) {
            'GBP' => '£',
            'EUR' => '€',
            'USD' => '$',
            'CAD' => 'CA$',
            'AUD' => 'A$',
            default => filled($currency) ? (string) $currency.' ' : null,
        };
    }
}
