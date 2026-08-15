<?php

namespace Tests\Feature;

use App\Livewire\PropertyList;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 08 of the Survey Sheet rollout: search, filters and the results pane.
 *
 * A narrowed result set is never a surprise: what is applied is on screen, each
 * one can be lifted, and an empty page names the next move and what it returns.
 */
class ListingFiltersTest extends TestCase
{
    use RefreshDatabase;

    private function stock(): void
    {
        Property::factory()->create([
            'title' => 'Cheap Terrace', 'price' => 180_000, 'bedrooms' => 2,
            'property_type' => 'House', 'status' => 'For Sale',
        ]);
        Property::factory()->create([
            'title' => 'Grand Manor', 'price' => 2_400_000, 'bedrooms' => 8,
            'property_type' => 'House', 'status' => 'For Sale',
        ]);
        Property::factory()->create([
            'title' => 'Town Flat', 'price' => 320_000, 'bedrooms' => 1,
            'property_type' => 'Apartment', 'status' => 'For Sale',
        ]);
    }

    public function test_an_applied_filter_is_visible_as_a_set(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 2)
            ->set('propertyType', 'house')
            ->assertSee('2+ bedrooms')
            ->assertSee('House');
    }

    public function test_nothing_is_listed_as_applied_when_nothing_is(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)->assertDontSee('Clear all');
    }

    public function test_a_filter_can_be_lifted_on_its_own(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 2)
            ->set('propertyType', 'apartment')
            ->assertDontSee('Cheap Terrace')
            ->call('clearFilter', 'propertyType')
            ->assertSee('Cheap Terrace')
            ->assertSee('2+ bedrooms');
    }

    public function test_every_filter_can_be_lifted_at_once(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 8)
            ->set('maxPrice', 200_000)
            ->call('clearFilters')
            ->assertSee('Cheap Terrace')
            ->assertSee('Grand Manor')
            ->assertSee('Town Flat');
    }

    public function test_the_result_count_is_on_screen(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->assertSee('3 homes')
            ->set('minBedrooms', 2)
            ->assertSee('2 homes');
    }

    public function test_one_result_is_counted_in_the_singular(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)->set('minBedrooms', 8)->assertSee('1 home');
    }

    /**
     * An empty page is an invitation, not a dead end: it names the filter to
     * lift and how many homes that returns.
     */
    public function test_an_empty_result_names_the_next_move_and_its_count(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 2)
            ->set('maxPrice', 100)
            ->assertSee('No homes match')
            ->assertSee('Clear the maximum price')
            ->assertSee('2 homes');
    }

    public function test_lifting_the_named_filter_actually_returns_that_many(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 2)
            ->set('maxPrice', 100)
            ->call('clearFilter', 'maxPrice')
            ->assertSee('2 homes')
            ->assertSee('Cheap Terrace')
            ->assertSee('Grand Manor');
    }

    /**
     * Filter state has to be shareable, so it belongs in the URL.
     */
    public function test_filter_state_survives_a_reload(): void
    {
        $this->stock();

        $html = $this->get('/properties?minBedrooms=2&propertyType=house')->assertOk()->getContent();

        $this->assertStringContainsString('Cheap Terrace', $html);
        $this->assertStringNotContainsString('Town Flat', $html);
        $this->assertStringContainsString('2+ bedrooms', $html, 'the applied set should reflect the URL');
    }

    /**
     * Pins and cards have to be the same set. A map showing everything on the
     * books beside a narrowed list invites the reader to think it is not.
     */
    public function test_the_map_shows_what_the_filters_returned(): void
    {
        Property::factory()->create([
            'title' => 'Mapped Match', 'bedrooms' => 4, 'status' => 'For Sale',
            'latitude' => 51.45, 'longitude' => -0.97,
        ]);
        Property::factory()->create([
            'title' => 'Mapped Miss', 'bedrooms' => 1, 'status' => 'For Sale',
            'latitude' => 51.46, 'longitude' => -0.98,
        ]);

        $html = $this->get('/properties?minBedrooms=4')->assertOk()->getContent();

        preg_match('/data-properties="([^"]*)"/', $html, $payload);
        $mapped = html_entity_decode($payload[1] ?? '');

        $this->assertStringContainsString('Mapped Match', $mapped);
        $this->assertStringNotContainsString('Mapped Miss', $mapped, 'the map is showing filtered-out stock');
    }

    /**
     * The initial render was only half the story: wire:ignore means a filter
     * change cannot re-render the pins, so they have to be pushed. Without it
     * the map keeps the pre-filter set beside a narrowed list.
     */
    public function test_changing_a_filter_pushes_the_new_pins_to_the_map(): void
    {
        Property::factory()->create([
            'title' => 'Mapped Match', 'bedrooms' => 4, 'status' => 'For Sale',
            'latitude' => 51.45, 'longitude' => -0.97,
        ]);
        Property::factory()->create([
            'title' => 'Mapped Miss', 'bedrooms' => 1, 'status' => 'For Sale',
            'latitude' => 51.46, 'longitude' => -0.98,
        ]);

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 4)
            ->assertDispatched('property-map-updated', function (string $event, array $payload) {
                $titles = array_column($payload['properties'], 'title');

                return in_array('Mapped Match', $titles, true)
                    && ! in_array('Mapped Miss', $titles, true);
            });
    }

    /**
     * Every filter that narrows the query has to appear in the applied set. One
     * that does not is invisible, uncleavable by "Clear all", and makes the
     * empty state claim nothing is listed when plenty is.
     */
    public function test_every_filter_that_narrows_the_query_is_visible(): void
    {
        $component = new PropertyList;
        $bound = array_keys((new \ReflectionClass($component))->getDefaultProperties()['queryString']);

        $reflected = new \ReflectionMethod($component, 'appliedFilters');
        $source = file_get_contents((new \ReflectionClass($component))->getFileName());
        $body = implode("\n", array_slice(
            explode("\n", $source),
            $reflected->getStartLine(),
            $reflected->getEndLine() - $reflected->getStartLine()
        ));

        foreach ($bound as $filter) {
            $this->assertStringContainsString(
                '$this->'.$filter,
                $body,
                "[{$filter}] narrows the results but never shows as an applied filter"
            );
        }
    }

    public function test_clear_all_lifts_a_filter_that_has_no_chip_of_its_own(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('minTransitScore', 99)
            ->set('country', 'ZZ')
            ->call('clearFilters')
            ->assertSee('Cheap Terrace')
            ->assertSee('3 homes');
    }

    /**
     * An empty page caused by a filter must never say nothing is listed.
     */
    public function test_an_empty_page_never_blames_the_stock_for_a_filter(): void
    {
        $this->stock();

        Livewire::test(PropertyList::class)
            ->set('country', 'ZZ')
            ->assertDontSee('Nothing is listed right now')
            ->assertSee('Clear');
    }

    /**
     * A cleared filter should leave the URL as it was before it was applied,
     * or a "cleared" page produces a link nobody can share cleanly.
     */
    public function test_clearing_a_text_filter_restores_its_default(): void
    {
        $this->stock();

        $component = Livewire::test(PropertyList::class)
            ->set('search', 'terrace')
            ->call('clearFilter', 'search');

        $this->assertSame('', $component->get('search'));

        $component->set('propertyType', 'house')->call('clearFilter', 'propertyType');

        $this->assertSame('', $component->get('propertyType'));
    }

    /**
     * countWithout is reachable as a Livewire action from the browser, so it
     * has to refuse a name that is not an applied filter.
     */
    public function test_counting_without_an_unknown_filter_is_refused(): void
    {
        $this->stock();

        $component = Livewire::test(PropertyList::class);

        $this->assertSame(0, $component->instance()->countWithout('nonsense'));
        $this->assertSame(0, $component->instance()->countWithout('propertyFeatureService'));
    }

    /**
     * The dispatch used to live in the properties getter, which the view only
     * reads when there are results — so an empty result set left the pre-filter
     * pins beside "No homes match these filters", the one moment the map is
     * most obviously wrong.
     */
    public function test_an_empty_result_still_clears_the_map(): void
    {
        Property::factory()->create([
            'title' => 'Mapped Home', 'bedrooms' => 2, 'status' => 'For Sale',
            'latitude' => 51.45, 'longitude' => -0.97,
        ]);

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 9)
            ->assertSee('No homes match')
            ->assertDispatched('property-map-updated', function (string $event, array $payload) {
                return $payload['properties'] === []
                    && str_contains($payload['label'], '0');
            });
    }

    public function test_the_page_loads_no_third_party_image(): void
    {
        $html = $this->get('/properties')->assertOk()->getContent();

        foreach (['vecteezy.com', 'unsplash.com', 'placeholder.com', 'via.placeholder'] as $host) {
            $this->assertStringNotContainsString($host, $html, "the page hotlinks {$host}");
        }
    }
}
