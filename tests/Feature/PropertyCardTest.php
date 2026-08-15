<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * Ticket 05 of the Survey Sheet rollout: the property card and its disclosure
 * strip — the signature element. Where a listing card normally carries a
 * FEATURED flash, this one carries five facts pulled from the record.
 */
class PropertyCardTest extends TestCase
{
    use RefreshDatabase;

    private function render(array $attributes = []): string
    {
        $property = Property::factory()->create(array_merge([
            'title' => 'Alexandra Road',
            'price' => 565000,
            'area_sqft' => 1240,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'year_built' => 1904,
            'property_type' => 'House',
            'status' => 'For Sale',
            'list_date' => now()->subDays(2),
            'sold_date' => null,
            'epc' => ['rating' => 'B', 'score' => 84],
            'transit_score' => 78,
        ], $attributes));

        return Blade::render(
            '<x-property-card :property="$property" />',
            ['property' => $property->fresh()]
        );
    }

    public function test_the_card_states_the_price_and_the_home(): void
    {
        $html = $this->render();

        $this->assertStringContainsString('565,000', $html);
        $this->assertStringContainsString('Alexandra Road', $html);
        $this->assertStringContainsString('3', $html);
        $this->assertStringContainsString('1,240', $html);
    }

    public function test_the_disclosure_strip_carries_five_facts(): void
    {
        $html = $this->render();

        foreach (['EPC', '£/sq ft', 'Listed', 'Built', 'Transit'] as $label) {
            $this->assertStringContainsString($label, $html, "the strip is missing [{$label}]");
        }

        $this->assertStringContainsString('456', $html, 'price per square foot');
        $this->assertStringContainsString('2 days', $html, 'days listed');
        $this->assertStringContainsString('1904', $html, 'year built');
    }

    public function test_the_strip_states_the_energy_band_with_its_letter(): void
    {
        $html = $this->render();

        $this->assertStringContainsString('bg-epc-b', $html);
        $this->assertStringContainsString('84', $html);
    }

    /**
     * A missing value says so. Rendering 0 would claim the property is free,
     * brand new, or has no floor area, none of which the record actually says.
     *
     * price, area_sqft and list_date are NOT NULL in the schema, so the ones
     * that can genuinely be absent are the energy certificate, the year built
     * and the transit score. An area of 0 is legal and stands in for unmeasured.
     */
    public function test_a_missing_fact_says_not_supplied_rather_than_zero(): void
    {
        $html = $this->render([
            'area_sqft' => 0,
            'year_built' => null,
            'epc' => null,
            'energy_rating' => null,
            'energy_score' => null,
            'transit_score' => null,
        ]);

        $this->assertGreaterThanOrEqual(
            4,
            substr_count($html, 'Not supplied'),
            'every unheld fact must say so'
        );
        $this->assertStringNotContainsString('>0<', $html);
        $this->assertStringNotContainsString('0/100', $html);
    }

    /**
     * The strip's whole value is that a column of results lines up down the
     * page. "Not supplied" is wider than a ~60px cell at 11.5px mono, so it
     * truncated to "Not supp…" and wrapped, making one card's strip taller than
     * its neighbours. The marker holds the row height and keeps the meaning.
     */
    public function test_a_missing_strip_value_does_not_wrap_the_cell(): void
    {
        $html = $this->render(['epc' => null, 'energy_rating' => null, 'transit_score' => null]);

        preg_match('/<dl[^>]*>.*?<\/dl>/s', $html, $strip);

        $this->assertNotEmpty($strip, 'the disclosure strip should render');
        $this->assertStringContainsString('&mdash;', $strip[0]);
        $this->assertStringNotContainsString(
            '>Not supplied<',
            $strip[0],
            'the words belong in the label, not in the cell'
        );
        $this->assertStringContainsString('aria-label="Not supplied"', $strip[0]);
    }

    public function test_a_rental_reads_as_a_monthly_price(): void
    {
        $html = $this->render([
            'price' => 1150,
            'area_sqft' => 682,
            'status' => 'For Rent',
        ]);

        $this->assertStringContainsString('1,150', $html);
        $this->assertStringContainsString('pcm', $html);
        $this->assertStringContainsString('£/sq ft', $html);
    }

    /**
     * The whole card is one link target. Nesting the buttons inside that anchor
     * would be invalid and would swallow their clicks.
     */
    public function test_the_card_is_one_link_and_nests_no_anchors(): void
    {
        $html = $this->render();

        $this->assertMatchesRegularExpression('/<a[^>]*class="[^"]*absolute/', $html);
        $this->assertDoesNotMatchRegularExpression('/<a\b[^>]*>(?:(?!<\/a>).)*<a\b/s', $html);
    }

    public function test_a_property_without_a_photo_renders_the_drawn_placeholder(): void
    {
        $html = $this->render();

        $this->assertStringContainsString('aspect-3/2', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    public function test_the_status_chip_states_a_fact_with_a_number(): void
    {
        $html = $this->render(['list_date' => now()->subDays(2)]);

        $this->assertMatchesRegularExpression('/New\s*—\s*2 days/', $html);
    }

    public function test_a_long_standing_listing_carries_no_new_chip(): void
    {
        $html = $this->render(['list_date' => now()->subDays(90)]);

        $this->assertStringNotContainsString('New —', $html);
    }

    /**
     * The card is used on Livewire pages and static ones. Rendering a wishlist
     * button where nothing can handle the click would give the visitor a dead
     * control, so it is opt-in.
     */
    public function test_the_wishlist_control_is_opt_in(): void
    {
        $property = Property::factory()->create();

        $without = Blade::render('<x-property-card :property="$p" />', ['p' => $property]);
        $with = Blade::render('<x-property-card :property="$p" saveable />', ['p' => $property]);

        $this->assertStringNotContainsString('toggleFavorite', $without);
        $this->assertStringContainsString('toggleFavorite('.$property->id.')', $with);
        $this->assertStringContainsString('aria-pressed="false"', $with);
    }

    public function test_a_saved_property_shows_as_saved(): void
    {
        $property = Property::factory()->create();

        $html = Blade::render(
            '<x-property-card :property="$p" saveable :saved="true" />',
            ['p' => $property]
        );

        $this->assertStringContainsString('aria-pressed="true"', $html);
        $this->assertStringContainsString('Remove', $html);
    }

    /**
     * Rewriting the results grid dropped this control once already, leaving
     * PropertyList::toggleFavorite unreachable and no way to save a property
     * while browsing.
     */
    public function test_the_listings_page_keeps_the_wishlist_reachable(): void
    {
        Property::factory()->count(2)->create(['status' => 'For Sale']);

        $html = $this->get('/properties')->assertOk()->getContent();

        $this->assertStringContainsString('toggleFavorite', $html);
    }

    public function test_a_listing_added_today_does_not_read_as_zero_days(): void
    {
        $html = $this->render(['list_date' => now()]);

        $this->assertStringNotContainsString('0 days', $html);
        $this->assertStringNotContainsString('New — 0', $html);
        $this->assertStringContainsString('today', $html);
    }

    public function test_the_listings_page_renders_cards_in_the_sheet_grid(): void
    {
        Property::factory()->count(3)->create(['status' => 'For Sale']);

        $html = $this->get('/properties')->assertOk()->getContent();

        $this->assertStringContainsString('property-card', $html);
        $this->assertStringContainsString('sm:grid-cols-2', $html);
        $this->assertStringContainsString('lg:grid-cols-3', $html);
    }
}
