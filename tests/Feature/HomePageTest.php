<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 06 of the Survey Sheet rollout: the home page.
 *
 * A visitor lands and can immediately do the one thing they came for — search —
 * and see featured properties as the card from ticket 05, disclosure strips
 * included.
 */
class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function featured(int $count = 3): void
    {
        Property::factory()->count($count)->create([
            'is_featured' => true,
            'status' => 'For Sale',
            'epc' => ['rating' => 'B', 'score' => 84],
        ]);
    }

    public function test_the_search_leads_the_page(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString(route('property.list'), $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="search"/', $html);
    }

    /**
     * The search has to name what it accepts. "Search by location or property
     * name" tells a visitor nothing about whether a postcode will work.
     */
    public function test_the_search_says_what_it_accepts(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match('/<input[^>]*name="search"[^>]*placeholder="([^"]*)"/', $html, $m);

        $this->assertNotEmpty($m, 'the search field should carry an example');
        $this->assertStringContainsString('postcode', strtolower($m[1]));
    }

    public function test_featured_properties_render_as_the_property_card(): void
    {
        $this->featured();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(3, substr_count($html, 'property-card'));
        $this->assertStringContainsString('bg-epc-b', $html, 'disclosure strips included');
    }

    /**
     * One primary per view. Cards carry their own CTA on the listings page, but
     * three of them on the home page would compete with the search.
     */
    public function test_the_page_has_exactly_one_primary_action(): void
    {
        $this->featured();

        $html = $this->get('/')->assertOk()->getContent();

        // Count the fill itself, not hover:bg-action-hover, which contains it.
        preg_match_all('/(?<![-\w:])bg-action(?![-\w])/', $html, $fills);

        $this->assertCount(
            1,
            $fills[0],
            'the search is the only primary action on the home page'
        );
    }

    public function test_an_empty_featured_list_invites_a_search(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('property-card', $html);
        $this->assertMatchesRegularExpression('/[Bb]rowse|[Ss]ee all/', $html);
    }

    /**
     * The map is the heaviest thing on the page. It must not fetch tiles, and
     * must not ask for the visitor's location, before they have scrolled to it.
     */
    public function test_the_map_does_not_load_itself(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-map', $html);
        $this->assertStringContainsString('IntersectionObserver', $html);

        // Geolocation is allowed, but only behind a control the visitor presses.
        $this->assertStringContainsString('data-map-locate', $html);
        $this->assertLessThan(
            strpos($html, '.locate('),
            strpos($html, "addEventListener('click'"),
            'the only locate() call must sit inside a click handler'
        );
    }

    public function test_the_map_sits_below_the_featured_properties(): void
    {
        $this->featured(1);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'data-map'),
            strpos($html, 'property-card'),
            'the heaviest element on the page belongs below the fold'
        );
    }

    public function test_the_page_loads_livewire_once(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertLessThanOrEqual(1, substr_count($html, 'livewire.js'));
    }

    /**
     * Featured cards read media, price per square foot and the energy record
     * for every row. Without eager loading that is a query per card.
     */
    public function test_featured_properties_do_not_query_per_card(): void
    {
        $this->featured();

        \DB::enableQueryLog();
        $this->get('/')->assertOk();
        $queries = count(\DB::getQueryLog());
        \DB::disableQueryLog();

        $this->assertLessThan(30, $queries, "the home page ran {$queries} queries");
    }
}
