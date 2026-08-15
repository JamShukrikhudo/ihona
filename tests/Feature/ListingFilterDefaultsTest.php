<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Ticket 16 of the Survey Sheet rollout: a filter nobody touched must not hide
 * anything.
 *
 * Every one of these defaults was a real bound applied unconditionally, so the
 * listings page quietly dropped stock at the top of the market — the largest
 * and most expensive homes, which are exactly the ones an agency wants seen.
 */
class ListingFilterDefaultsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The component caches its result set for 15 minutes by a key built
        // from the filters; a warm cache would mask a filter change.
        Cache::flush();
    }

    /**
     * @return array<string, array{array<string, mixed>, string}>
     */
    public static function propertiesBeyondTheDefaults(): array
    {
        return [
            'priced above the old cap' => [['price' => 4_500_000], 'a £4.5m house'],
            'more bedrooms than the old cap' => [['bedrooms' => 14], 'a 14-bedroom house'],
            'more bathrooms than the old cap' => [['bathrooms' => 12], 'a 12-bathroom house'],
            'larger than the old cap' => [['area_sqft' => 18_000], 'an 18,000 sq ft house'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('propertiesBeyondTheDefaults')]
    public function test_an_untouched_search_hides_nothing(array $attributes, string $what): void
    {
        $property = Property::factory()->create(array_merge([
            'title' => 'Beyond The Defaults House',
            'status' => 'For Sale',
        ], $attributes));

        $html = $this->get('/properties')->assertOk()->getContent();

        $this->assertStringContainsString(
            $property->title,
            $html,
            "{$what} is missing from an unfiltered listings page"
        );
    }

    public function test_an_untouched_search_returns_every_property(): void
    {
        Property::factory()->count(3)->create(['status' => 'For Sale']);
        Property::factory()->create(['price' => 9_000_000, 'bedrooms' => 20, 'area_sqft' => 40_000]);

        $html = $this->get('/properties')->assertOk()->getContent();

        $this->assertSame(
            Property::count(),
            substr_count($html, 'property-card'),
            'an unfiltered page should show every property the model can see'
        );
    }

    /**
     * A default that is a real bound reads as "unset" to whoever writes the
     * next filter, and the query string then disagrees with it.
     */
    public function test_no_maximum_filter_defaults_to_a_bound(): void
    {
        $component = new \App\Livewire\PropertyList;

        foreach (['maxPrice', 'maxBedrooms', 'maxBathrooms', 'maxArea'] as $filter) {
            $this->assertNull(
                $component->{$filter},
                "[{$filter}] defaults to a bound, so it narrows a search nobody asked to narrow"
            );
        }
    }

    public function test_the_query_string_defaults_match_the_property_defaults(): void
    {
        $component = new \App\Livewire\PropertyList;
        $queryString = (new \ReflectionClass($component))
            ->getDefaultProperties()['queryString'];

        foreach ($queryString as $filter => $options) {
            if (! array_key_exists('except', $options) || ! property_exists($component, $filter)) {
                continue;
            }

            $this->assertSame(
                $component->{$filter},
                $options['except'],
                "[{$filter}] declares an except-value the property default does not match, "
                .'so the filter is either applied when it looks unset or hidden from the URL when it is not'
            );
        }
    }
}
