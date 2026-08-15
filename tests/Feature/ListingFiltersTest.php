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

    public function test_the_page_loads_no_third_party_image(): void
    {
        $html = $this->get('/properties')->assertOk()->getContent();

        foreach (['vecteezy.com', 'unsplash.com', 'placeholder.com', 'via.placeholder'] as $host) {
            $this->assertStringNotContainsString($host, $html, "the page hotlinks {$host}");
        }
    }
}
