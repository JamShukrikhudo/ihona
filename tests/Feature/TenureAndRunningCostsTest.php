<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 18 of the Survey Sheet rollout: tenure and running costs.
 *
 * What a property costs to hold is the question asked immediately after the
 * price, and a leasehold with 68 years left is a materially different
 * proposition from the same flat with 900 — 80 years is the point below which
 * the lease starts costing real money to extend. None of it could be shown
 * because none of it was stored.
 */
class TenureAndRunningCostsTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $attributes = []): Property
    {
        return Property::factory()->create(array_merge([
            'status' => 'For Sale',
            'title' => 'Kendrick Road, Reading RG1',
        ], $attributes));
    }

    public function test_the_record_holds_them(): void
    {
        $property = $this->property([
            'council_tax_band' => 'D',
            'tenure' => 'leasehold',
            'lease_years_remaining' => 68,
            'service_charge' => 1450.00,
            'ground_rent' => 250.00,
        ])->fresh();

        $this->assertSame('D', $property->council_tax_band);
        $this->assertSame('leasehold', $property->tenure);
        $this->assertSame(68, $property->lease_years_remaining);
        $this->assertSame('1450.00', (string) $property->service_charge);
        $this->assertSame('250.00', (string) $property->ground_rent);
    }

    public function test_a_leasehold_shows_the_years_it_has_left(): void
    {
        $property = $this->property(['tenure' => 'leasehold', 'lease_years_remaining' => 68]);

        $this->assertSame('Leasehold, 68 years remaining', $property->tenureForHumans());
    }

    public function test_a_freehold_does_not_claim_a_lease_length(): void
    {
        $property = $this->property(['tenure' => 'freehold', 'lease_years_remaining' => 68]);

        $this->assertSame('Freehold', $property->tenureForHumans());
        $this->assertFalse($property->hasShortLease());
    }

    /**
     * 80 years is the threshold: below it the freeholder can charge marriage
     * value to extend, which is the difference between an inconvenience and a
     * five-figure bill.
     */
    public function test_a_short_lease_is_flagged(): void
    {
        $this->assertTrue($this->property(['tenure' => 'leasehold', 'lease_years_remaining' => 79])->hasShortLease());
        $this->assertFalse($this->property(['tenure' => 'leasehold', 'lease_years_remaining' => 80])->hasShortLease());
        $this->assertFalse($this->property(['tenure' => 'leasehold', 'lease_years_remaining' => null])->hasShortLease());
    }

    public function test_a_short_lease_is_visible_on_the_card(): void
    {
        $this->property(['tenure' => 'leasehold', 'lease_years_remaining' => 68]);

        $this->get('/properties')->assertOk()->assertSee('68-year lease');
    }

    public function test_a_long_lease_does_not_shout(): void
    {
        $this->property(['tenure' => 'leasehold', 'lease_years_remaining' => 950]);

        $this->get('/properties')->assertOk()->assertDontSee('950-year lease');
    }

    public function test_the_disclosure_panel_carries_them(): void
    {
        $property = $this->property([
            'council_tax_band' => 'D',
            'tenure' => 'leasehold',
            'lease_years_remaining' => 68,
            'service_charge' => 1450.00,
            'ground_rent' => 250.00,
        ]);

        $page = $this->get('/properties/'.$property->id)->assertOk();

        $page->assertSee('Council tax');
        $page->assertSee('Band D');
        $page->assertSee('Tenure');
        $page->assertSee('Leasehold, 68 years remaining');
        $page->assertSee('Service charge');
        $page->assertSee('£1,450 a year', false);
        $page->assertSee('Ground rent');
        $page->assertSee('£250 a year', false);
    }

    /**
     * Every one of these is optional, and a blank cell reads as a fact of zero
     * rather than a fact not held.
     */
    public function test_what_is_not_held_is_marked_not_supplied(): void
    {
        $property = $this->property([
            'council_tax_band' => null,
            'tenure' => null,
            'service_charge' => null,
        ]);

        $page = $this->get('/properties/'.$property->id)->assertOk();

        $page->assertSee('Council tax');
        $page->assertSee('Not supplied');

        // Every band, rather than the bare word: the cell's own source line
        // reads "Band as recorded, not the bill".
        foreach (range('A', 'I') as $band) {
            $page->assertDontSee('Band '.$band);
        }
    }

    /**
     * A service charge of zero is a fact — some leases genuinely have none —
     * and it must not read as a missing one.
     */
    public function test_a_charge_of_zero_is_stated_rather_than_blanked(): void
    {
        $property = $this->property(['tenure' => 'leasehold', 'ground_rent' => 0]);

        $this->get('/properties/'.$property->id)->assertOk()->assertSee('Peppercorn');
    }

    public function test_the_energy_cost_comes_from_the_certificate(): void
    {
        $property = $this->property([
            'epc' => ['rating' => 'C', 'score' => 72, 'annual_energy_cost' => 1240],
        ]);

        $this->assertSame(1240.0, $property->annualEnergyCost());
        $this->get('/properties/'.$property->id)->assertOk()->assertSee('£1,240 a year', false);
    }

    /**
     * A certificate that itemises its costs is still the source; adding them up
     * is arithmetic, not invention.
     */
    public function test_an_itemised_certificate_is_totalled(): void
    {
        $property = $this->property([
            'epc' => ['rating' => 'C', 'heating_cost' => 800, 'hot_water_cost' => 150, 'lighting_cost' => 90],
        ]);

        $this->assertSame(1040.0, $property->annualEnergyCost());
    }

    /**
     * A band is not a bill. Without a costed certificate the figure is unknown,
     * and guessing one from the rating would be a number with a confident font.
     */
    public function test_a_band_alone_does_not_produce_a_figure(): void
    {
        $property = $this->property(['epc' => ['rating' => 'C', 'score' => 72]]);

        $this->assertNull($property->annualEnergyCost());
    }

    /**
     * Sale state lives in `status`; `sold_date` is nullable and rarely written,
     * and the card gated everything on that alone. A property sold this morning
     * with today's list date carried a green "New — today" chip and a live
     * "Book a viewing" button.
     */
    public function test_a_sold_listing_is_not_advertised_as_new(): void
    {
        $this->property([
            'status' => 'Sold',
            'sold_date' => null,
            'list_date' => now(),
            'title' => 'Alexandra Road, Reading RG1',
        ]);

        $page = $this->get('/properties')->assertOk();

        $page->assertDontSee('New — today', false);
        $page->assertSee('Sold');
    }

    public function test_a_sold_listing_does_not_offer_a_viewing(): void
    {
        $property = $this->property(['status' => 'Sold', 'sold_date' => null, 'list_date' => now()]);

        $html = $this->get('/properties')->assertOk()->getContent();

        $this->assertStringNotContainsString(
            route('property.book', $property->id),
            $html,
            'a sold property still offers a viewing'
        );
    }

    public function test_a_listing_still_for_sale_keeps_its_booking_link(): void
    {
        $property = $this->property(['status' => 'For Sale', 'list_date' => now()]);

        $this->get('/properties')->assertOk()->assertSee(route('property.book', $property->id), false);
    }

    public function test_staff_can_edit_every_one_of_them(): void
    {
        $source = file_get_contents(app_path('Filament/Staff/Resources/Properties/PropertyResource.php'));

        foreach (['council_tax_band', 'tenure', 'lease_years_remaining', 'service_charge', 'ground_rent'] as $field) {
            $this->assertStringContainsString("'{$field}'", $source, "[{$field}] cannot be edited by staff");
        }
    }
}
