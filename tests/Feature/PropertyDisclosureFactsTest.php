<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 05 of the Survey Sheet rollout: the facts behind the disclosure strip.
 *
 * These are derived, never stored, so they cannot drift out of step with the
 * price and the floor area. Where the record does not hold a value, the fact is
 * null and the strip says so — never 0, never a blank cell.
 */
class PropertyDisclosureFactsTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $attributes = []): Property
    {
        return Property::factory()->make($attributes);
    }

    public function test_price_per_square_foot_divides_price_by_area(): void
    {
        $property = $this->property([
            'price' => 565000,
            'area_sqft' => 1240,
            'status' => 'For Sale',
        ]);

        $this->assertSame(456.0, $property->pricePerSquareFoot());
        $this->assertSame('456', $property->pricePerSquareFootForHumans());
    }

    public function test_price_per_square_foot_is_null_without_an_area(): void
    {
        foreach ([null, 0] as $area) {
            $property = $this->property(['price' => 565000, 'area_sqft' => $area]);

            $this->assertNull(
                $property->pricePerSquareFoot(),
                'a missing floor area must not divide by zero or report £0'
            );
        }
    }

    public function test_price_per_square_foot_is_null_without_a_price(): void
    {
        $this->assertNull($this->property(['price' => null, 'area_sqft' => 1240])->pricePerSquareFoot());
    }

    /**
     * A rental price is monthly, so the figure is monthly too. Quoting a
     * rental's monthly rent as if it were a sale price per square foot would be
     * out by a factor of well over a hundred.
     */
    public function test_price_per_square_foot_reads_monthly_for_a_rental(): void
    {
        $rental = $this->property([
            'price' => 1150,
            'area_sqft' => 682,
            'status' => 'For Rent',
        ]);

        $this->assertTrue($rental->isRental());

        // 1150 / 682 = 1.686. Rounding that to a whole pound would throw away
        // the only part of a monthly rate anyone actually compares.
        $this->assertSame(1.69, $rental->pricePerSquareFoot());
        $this->assertSame('1.69 pcm', $rental->pricePerSquareFootForHumans());
        $this->assertSame('£/sq ft', $rental->pricePerSquareFootLabel());
    }

    /**
     * The platform writes statuses in more than one shape: the API and staff
     * panel use snake_case, older rows carry the title-case forms. A rental
     * missed here renders a monthly rent as though it were a sale price.
     */
    public function test_a_rental_is_recognised_in_every_spelling_the_app_writes(): void
    {
        foreach (['to_let', 'let_agreed', 'let', 'For Rent', 'for_rent', 'TO LET', 'Let Agreed'] as $status) {
            $this->assertTrue(
                $this->property(['status' => $status])->isRental(),
                "[{$status}] should read as a rental"
            );
        }
    }

    public function test_a_sale_status_is_never_read_as_a_rental(): void
    {
        foreach (['for_sale', 'under_offer', 'sstc', 'exchanged', 'sold', 'For Sale'] as $status) {
            $this->assertFalse(
                $this->property(['status' => $status])->isRental(),
                "[{$status}] should not read as a rental"
            );
        }
    }

    /**
     * A euro-priced listing must not show a euro price above a cell labelled
     * in pounds.
     */
    public function test_the_rate_label_carries_the_listing_currency(): void
    {
        $this->assertSame(
            '€/sq ft',
            $this->property(['currency' => 'EUR', 'status' => 'For Sale'])->pricePerSquareFootLabel()
        );
        $this->assertSame(
            '€/sq ft',
            $this->property(['currency' => 'EUR', 'status' => 'to_let'])->pricePerSquareFootLabel()
        );
    }

    /**
     * Band and score must come from the same record. Choosing independently
     * badges a certificate's band B with a legacy column's band-D score.
     */
    public function test_the_score_comes_from_whichever_record_supplied_the_band(): void
    {
        $fromCertificate = $this->property([
            'epc' => ['rating' => 'B'],
            'energy_rating' => 'D',
            'energy_score' => 55,
        ]);

        $this->assertSame('B', $fromCertificate->energyBand());
        $this->assertNull(
            $fromCertificate->energyScore(),
            'a certificate without a score must not borrow the legacy column'
        );

        $fromColumn = $this->property([
            'epc' => ['score' => 91],
            'energy_rating' => 'C',
            'energy_score' => 71,
        ]);

        $this->assertSame('C', $fromColumn->energyBand());
        $this->assertSame(71, $fromColumn->energyScore());
    }

    /**
     * Without the mark, a high-value rental is indistinguishable from a sale:
     * £12,000 pcm over 900 sq ft reads "13", exactly like a £13/sq ft sale.
     */
    public function test_a_high_value_rental_is_not_mistaken_for_a_sale_rate(): void
    {
        $rental = $this->property(['price' => 12000, 'area_sqft' => 900, 'status' => 'to_let']);
        $sale = $this->property(['price' => 11700, 'area_sqft' => 900, 'status' => 'For Sale']);

        $this->assertSame('13 pcm', $rental->pricePerSquareFootForHumans());
        $this->assertSame('13', $sale->pricePerSquareFootForHumans());
    }

    public function test_a_sale_is_not_a_rental(): void
    {
        foreach (['For Sale', 'Sold'] as $status) {
            $this->assertFalse($this->property(['status' => $status])->isRental());
        }

        foreach (['For Rent', 'Rented'] as $status) {
            $this->assertTrue($this->property(['status' => $status])->isRental());
        }
    }

    public function test_sale_price_per_square_foot_is_labelled_plainly(): void
    {
        $this->assertSame('£/sq ft', $this->property(['status' => 'For Sale'])->pricePerSquareFootLabel());
    }

    public function test_days_listed_counts_from_the_listing_date(): void
    {
        $property = $this->property(['list_date' => now()->subDays(46), 'sold_date' => null]);

        $this->assertSame(46, $property->daysListed());
    }

    /**
     * Once a property has sold, days listed stops at the sale. Otherwise a sold
     * listing would keep ageing forever and read as stale stock.
     */
    public function test_days_listed_stops_at_the_sale_date(): void
    {
        $property = $this->property([
            'list_date' => now()->subDays(100),
            'sold_date' => now()->subDays(40),
            'status' => 'Sold',
        ]);

        $this->assertSame(60, $property->daysListed());
    }

    public function test_days_listed_is_null_without_a_listing_date(): void
    {
        $this->assertNull($this->property(['list_date' => null])->daysListed());
    }

    public function test_the_listing_currency_wins_over_the_site_symbol(): void
    {
        $this->assertSame('£', $this->property(['currency' => 'GBP'])->currencySymbol());
        $this->assertSame('€', $this->property(['currency' => 'EUR'])->currencySymbol());
    }

    /**
     * A code is not a symbol: printed flush against the number it read
     * "CHF1,234,000". It keeps a space until the code earns a symbol.
     */
    public function test_an_unmapped_currency_falls_back_to_its_code(): void
    {
        $this->assertSame('CHF ', $this->property(['currency' => 'CHF'])->currencySymbol());
    }

    public function test_energy_band_prefers_the_certificate_record(): void
    {
        $property = $this->property([
            'epc' => ['rating' => 'B', 'score' => 84],
            'energy_rating' => 'D',
            'energy_score' => 55,
        ]);

        $this->assertSame('B', $property->energyBand());
        $this->assertSame(84, $property->energyScore());
    }

    public function test_energy_band_falls_back_to_the_column(): void
    {
        $property = $this->property([
            'epc' => null,
            'energy_rating' => 'C',
            'energy_score' => 71,
        ]);

        $this->assertSame('C', $property->energyBand());
        $this->assertSame(71, $property->energyScore());
    }

    public function test_energy_band_is_null_when_nothing_is_recorded(): void
    {
        $property = $this->property(['epc' => null, 'energy_rating' => null, 'energy_score' => null]);

        $this->assertNull($property->energyBand());
        $this->assertNull($property->energyScore());
    }

    public function test_energy_band_is_normalised_to_a_single_upper_case_letter(): void
    {
        $this->assertSame('B', $this->property(['epc' => ['rating' => ' b ']])->energyBand());
    }

    public function test_an_out_of_range_energy_band_is_treated_as_missing(): void
    {
        $this->assertNull($this->property(['epc' => ['rating' => 'Z']])->energyBand());
    }
}
