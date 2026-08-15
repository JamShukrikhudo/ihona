<?php

namespace App\View\Components;

use App\Models\Property;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PropertyMap extends Component
{
    public Collection $properties;

    /**
     * Honours what the caller passes.
     *
     * This used to discard the argument and re-query unconditionally, without
     * selecting `price` — so every marker popup on the home page read "£NaN",
     * and the controller's own map query was dead code that ran on every
     * request and was thrown away.
     *
     * Callers with nothing to pass (the advanced search) still get a sensible
     * default, now bounded: the old query had no limit at all.
     */
    public function __construct($properties = null)
    {
        $this->properties = Collection::wrap(
            $properties !== null ? $properties : self::defaults()
        )->map(fn ($property) => self::point($property))->values();
    }

    /**
     * A Livewire component re-renders on every debounced keystroke, and this
     * component sits inside one. Without memoising, each of those paid for a
     * 500-row query and a full json_encode whose output wire:ignore then
     * discarded.
     *
     * Held in the container rather than a static, so it lasts exactly one
     * request. A static outlived the request under Octane and outlived the
     * test that populated it, which is a stale map either way.
     */
    private static function defaults(): Collection
    {
        $key = self::class.':defaults';

        if (! app()->bound($key)) {
            app()->instance($key, self::mappable()->get());
        }

        return app($key);
    }

    /**
     * Shaped here so the view ships only what a marker needs, with the
     * listing's own currency symbol rather than the site-wide one — the same
     * rule the property card follows.
     */
    private static function point(mixed $property): array
    {
        $get = fn (string $key) => is_array($property) ? ($property[$key] ?? null) : ($property->{$key} ?? null);

        return [
            'id' => $get('id'),
            'title' => $get('title'),
            'price' => $get('price'),
            'currency' => $property instanceof Property
                ? $property->currencySymbol()
                : ($get('currency') ?: null),
            'latitude' => $get('latitude'),
            'longitude' => $get('longitude'),
        ];
    }

    public static function mappable(): \Illuminate\Database\Eloquent\Builder
    {
        return Property::query()
            ->select(['id', 'title', 'price', 'currency', 'latitude', 'longitude'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->limit(500);
    }

    public function render()
    {
        return view('components.property-map', ['properties' => $this->properties]);
    }
}
