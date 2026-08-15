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

    /**
     * The dispatched payload has to be shaped like the server-rendered one, or
     * a price reads "£565,000" on first paint and "GBP565,000" the moment a
     * filter changes.
     */
    public function test_the_pushed_pins_carry_a_currency_symbol(): void
    {
        Property::factory()->create([
            'title' => 'Mapped Home', 'bedrooms' => 4, 'status' => 'For Sale',
            'currency' => 'GBP', 'latitude' => 51.45, 'longitude' => -0.97,
        ]);

        Livewire::test(PropertyList::class)
            ->set('minBedrooms', 4)
            ->assertDispatched('property-map-updated', function (string $event, array $payload) {
                return ($payload['properties'][0]['currency'] ?? null) === '£';
            });
    }

    /**
     * Reached through the component, not the URL: Livewire owns its page state
     * and resets an out-of-range ?page on a plain GET. gotoPage does not.
     */
    public function test_a_page_beyond_the_results_is_not_an_empty_search(): void
    {
        Property::factory()->count(3)->create(['status' => 'For Sale']);

        Livewire::test(PropertyList::class)
            ->call('gotoPage', 99)
            ->assertSee('Nothing on this page')
            ->assertDontSee('No homes match these filters');
    }

    /**
     * The banner sat above the point where the list query runs, so a database
     * failure rendered as "no homes match these filters" — an outage presented
     * as an empty shop — and the real message surfaced on someone's next,
     * healthy visit.
     */
    public function test_a_query_failure_is_reported_rather_than_read_as_no_stock(): void
    {
        $source = file_get_contents(resource_path('views/livewire/property-list.blade.php'));

        $this->assertLessThan(
            strpos($source, '@if ($failure)'),
            strpos($source, '$results = $this->properties'),
            'the list has to be resolved before the banner that reports its failure'
        );
    }

    public function test_the_page_counts_its_results_once(): void
    {
        Property::factory()->count(3)->create(['status' => 'For Sale']);

        \DB::enableQueryLog();
        $this->get('/properties')->assertOk();
        $counts = collect(\DB::getQueryLog())
            ->filter(fn ($q) => str_contains(strtolower($q['query']), 'count(*)'))
            ->count();
        \DB::disableQueryLog();

        $this->assertLessThanOrEqual(
            1,
            $counts,
            "the page ran {$counts} count queries with the same filters"
        );
    }

    /**
     * The comment above $queryString says each except-value must equal the
     * property default. It did not hold: the numeric filters defaulted to null
     * while the controls that clear them submit '', so clearing "Any beds"
     * left ?minBedrooms= in the URL and the cleared page was not shareable.
     * Livewire also ignores an `except` of null outright.
     */
    public function test_every_cleared_filter_leaves_the_url(): void
    {
        $component = new \App\Livewire\PropertyList();

        $queryString = (new \ReflectionProperty($component, 'queryString'))->getValue($component);
        $defaults = (new \ReflectionClass($component))->getDefaultProperties();
        $defaultFor = (new \ReflectionMethod($component, 'defaultFor'));

        foreach ($queryString as $filter => $options) {
            $this->assertArrayHasKey('except', $options, "[{$filter}] has no except value");

            $this->assertNotNull(
                $options['except'],
                "[{$filter}] excepts null, which Livewire treats as unspecified"
            );

            $this->assertSame(
                $defaults[$filter],
                $options['except'],
                "[{$filter}] excepts a value its property never holds"
            );

            $this->assertSame(
                $defaults[$filter],
                $defaultFor->invoke($component, $filter),
                "[{$filter}] is cleared to something other than its default"
            );
        }
    }

    /**
     * The empty state suggests the one filter worth loosening, which costs a
     * COUNT per applied filter. With eighteen filters bound to a live search
     * box that is a burst of queries per debounced keystroke.
     */
    public function test_the_empty_state_does_not_count_once_per_filter(): void
    {
        Property::factory()->count(3)->create(['status' => 'For Sale']);

        \DB::enableQueryLog();
        $this->get('/properties?search=nothingmatchesthis&minBedrooms=5&minPrice=99999999')->assertOk();
        $counts = collect(\DB::getQueryLog())
            ->filter(fn ($q) => str_contains(strtolower($q['query']), 'count(*)'))
            ->count();
        \DB::disableQueryLog();

        $this->assertLessThanOrEqual(
            6,
            $counts,
            "the empty state ran {$counts} count queries"
        );
    }

    public function test_the_page_loads_no_third_party_image(): void
    {
        $html = $this->get('/properties')->assertOk()->getContent();

        foreach (['vecteezy.com', 'unsplash.com', 'placeholder.com', 'via.placeholder'] as $host) {
            $this->assertStringNotContainsString($host, $html, "the page hotlinks {$host}");
        }
    }

    /**
     * A Livewire request never redirects, so a flash set during one is still
     * sitting in the session when the visitor opens their next full page — and
     * surfaces there as a red banner about a search they have already left.
     * Same reason the booking form stopped flashing.
     */
    public function test_no_message_is_flashed_from_a_livewire_request(): void
    {
        $source = file_get_contents(app_path('Livewire/PropertyList.php'));

        $this->assertStringNotContainsString(
            'session()->flash',
            $source,
            'a flash set here follows the visitor onto whatever page they open next'
        );
    }

    public function test_a_failed_query_says_so_on_the_page_it_failed_on(): void
    {
        $component = Livewire::test(PropertyList::class);

        $component->set('failure', 'Something went wrong finding those homes. Try the search again.')
            ->assertSee('Try the search again');

        $this->assertNull(session('error'), 'the failure was put in the session as well');
    }

    /**
     * The message carried the exception file and line when APP_DEBUG was on.
     * That is a stack trace in a red banner on a public listing page.
     */
    public function test_the_failure_never_carries_a_file_path(): void
    {
        $source = file_get_contents(app_path('Livewire/PropertyList.php'));

        $this->assertStringNotContainsString('getFile()', $source);
        $this->assertStringNotContainsString('error_details', $source);
    }
    /**
     * The card asked "is this one saved?" per result, so a signed-in visitor
     * paid twelve `select exists` on top of the listing query, the count and
     * the map query — on every debounced keystroke.
     */
    public function test_the_saved_state_costs_one_query_for_the_whole_page(): void
    {
        $user = \App\Models\User::factory()->create();
        $properties = Property::factory()->count(5)->create(['status' => 'For Sale']);

        \App\Models\Favorite::create([
            'user_id' => $user->id,
            'property_id' => $properties->first()->id,
        ]);

        $component = Livewire::actingAs($user)->test(PropertyList::class);

        \DB::enableQueryLog();
        $component->call('$refresh');
        $favouriteQueries = collect(\DB::getQueryLog())
            ->filter(fn ($q) => str_contains(strtolower($q['query']), 'favorites'))
            ->count();
        \DB::disableQueryLog();

        $this->assertLessThanOrEqual(1, $favouriteQueries, "the page ran {$favouriteQueries} favourite queries");
    }

    public function test_saving_a_home_is_reflected_on_the_same_page(): void
    {
        $user = \App\Models\User::factory()->create();
        $property = Property::factory()->create(['status' => 'For Sale']);

        $component = Livewire::actingAs($user)->test(PropertyList::class);

        $this->assertFalse($component->instance()->isFavorited($property->id));

        $component->call('toggleFavorite', $property->id);

        $this->assertTrue(
            $component->instance()->isFavorited($property->id),
            'the memoised list did not notice the favourite that was just added'
        );
    }
}
