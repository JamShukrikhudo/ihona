<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * The edges the happy path never reaches: sold stock, half-filled energy
 * records, listings dated in the future, and currencies outside the map.
 */
class DisclosureEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $attributes = []): Property
    {
        return Property::factory()->make(array_merge(['currency' => 'GBP'], $attributes));
    }

    /**
     * daysListed() deliberately stops at the sale, so a home listed two years
     * ago and sold two days later returns 2 forever — and the card badged it
     * "New — 2 days" for the rest of time.
     */
    public function test_a_sold_listing_is_never_badged_as_new(): void
    {
        $property = Property::factory()->create([
            'list_date' => now()->subYears(2),
            'sold_date' => now()->subYears(2)->addDays(2),
            'status' => 'Sold',
        ]);

        $html = Blade::render('<x-property-card :property="$p" />', ['p' => $property]);

        $this->assertSame(2, $property->daysListed(), 'the count itself is right');
        $this->assertStringNotContainsString('New —', $html, 'but it must not read as new');
    }

    public function test_a_genuinely_new_listing_is_still_badged(): void
    {
        $property = Property::factory()->create([
            'list_date' => now()->subDays(2),
            'sold_date' => null,
            'status' => 'For Sale',
        ]);

        $this->assertStringContainsString(
            'New —',
            Blade::render('<x-property-card :property="$p" />', ['p' => $property])
        );
    }

    /**
     * Band and score must agree, but throwing the score away when the
     * certificate holds only a rating loses a figure the record does hold.
     */
    public function test_a_score_is_kept_when_the_columns_agree_with_the_certificate(): void
    {
        $property = $this->property([
            'epc' => ['rating' => 'B'],
            'energy_rating' => 'B',
            'energy_score' => 84,
        ]);

        $this->assertSame('B', $property->energyBand());
        $this->assertSame(84, $property->energyScore(), 'the column agrees with the certificate');
    }

    public function test_a_score_is_dropped_when_the_columns_contradict_the_certificate(): void
    {
        $property = $this->property([
            'epc' => ['rating' => 'B'],
            'energy_rating' => 'D',
            'energy_score' => 55,
        ]);

        $this->assertSame('B', $property->energyBand());
        $this->assertNull($property->energyScore(), 'a band B must not wear a band D score');
    }

    public function test_a_listing_dated_in_the_future_is_not_yet_listed(): void
    {
        $property = $this->property(['list_date' => now()->addDays(10), 'sold_date' => null]);

        $this->assertNull($property->daysListed());
        $this->assertTrue($property->isComingSoon(), 'a future listing date is coming soon, not missing');
    }

    public function test_a_past_listing_is_not_coming_soon(): void
    {
        $this->assertFalse($this->property(['list_date' => now()->subDay()])->isComingSoon());
    }

    /**
     * An unmapped code was printed where a symbol belongs, so a Japanese
     * listing read "JPY1,234,000" and its rate label "JPY/sq ft".
     */
    public function test_an_unmapped_currency_is_not_printed_as_a_symbol(): void
    {
        $property = $this->property(['currency' => 'JPY']);

        $this->assertSame('JPY ', $property->currencySymbol());
        $this->assertSame('JPY /sq ft', $property->pricePerSquareFootLabel());
    }

    public function test_a_mapped_currency_still_has_no_gap(): void
    {
        $this->assertSame('£', $this->property(['currency' => 'GBP'])->currencySymbol());
        $this->assertSame('£/sq ft', $this->property(['currency' => 'GBP'])->pricePerSquareFootLabel());
    }

    /**
     * The strip exists so a reader can compare figures down a column. The
     * full-card overlay link painted over it, so none of it could be selected.
     */
    public function test_the_disclosure_strip_sits_above_the_card_overlay(): void
    {
        $property = Property::factory()->create();

        $html = Blade::render('<x-property-card :property="$p" />', ['p' => $property]);

        $this->assertMatchesRegularExpression(
            '/<dl[^>]*class="[^"]*relative[^"]*z-10/',
            $html,
            'the strip must be readable, not covered by the card link'
        );
    }
}
