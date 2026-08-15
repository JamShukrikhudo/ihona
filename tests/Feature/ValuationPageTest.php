<?php

namespace Tests\Feature;

use App\Livewire\PropertyValuationComponent;
use App\Models\Property;
use App\Models\PropertyValuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 23: what a visitor is told their home is worth.
 *
 * A machine valuation is a guess with a width. The page printed the midpoint to
 * two decimal places and threw the width away — the service computed a range on
 * every call and nothing ever read it.
 */
class ValuationPageTest extends TestCase
{
    use RefreshDatabase;

    private function property(): Property
    {
        return Property::factory()->create([
            'title' => 'Alexandra Road, Reading RG1',
            'price' => 565000,
            'area_sqft' => 1240,
            'currency' => 'GBP',
        ]);
    }

    private function valuation(Property $property, int $confidence = 80): PropertyValuation
    {
        return PropertyValuation::factory()->create([
            'property_id' => $property->id,
            'valuation_type' => 'neural_network',
            'estimated_value' => 500000,
            'confidence_level' => $confidence,
            'valuation_date' => now()->subDays(3),
            'valid_until' => now()->addMonths(3),
            'status' => 'active',
            'comparable_properties' => ['count' => 7, 'feature_importance' => ['area_sqft' => 42.0]],
            'location_factors' => ['market_trend' => 'rising', 'prediction_factors' => ['Large floor area']],
        ]);
    }

    public function test_the_band_straddles_the_estimate(): void
    {
        $range = $this->valuation($this->property())->range();

        $this->assertLessThan(500000, $range['low']);
        $this->assertGreaterThan(500000, $range['high']);
    }

    /**
     * The service had this backwards: width was confidence/200, so a model that
     * was 90% sure published a wider band than one that was 40% sure.
     */
    public function test_more_confidence_means_a_narrower_band(): void
    {
        $property = $this->property();

        $sure = PropertyValuation::factory()->make(['estimated_value' => 500000, 'confidence_level' => 95])->range();
        $unsure = PropertyValuation::factory()->make(['estimated_value' => 500000, 'confidence_level' => 30])->range();

        $this->assertLessThan(
            $unsure['high'] - $unsure['low'],
            $sure['high'] - $sure['low'],
        );
        $this->assertNotNull($property->id);
    }

    public function test_a_valuation_with_no_figure_has_no_band(): void
    {
        $this->assertNull(PropertyValuation::factory()->make(['estimated_value' => null])->range());
    }

    public function test_the_page_shows_the_band_rather_than_a_bare_figure(): void
    {
        $property = $this->property();
        $valuation = $this->valuation($property);
        $range = $valuation->range();

        Livewire::test(PropertyValuationComponent::class, ['propertyId' => $property->id])
            ->call('viewValuation', $valuation->id)
            ->assertSee(number_format($range['low']))
            ->assertSee(number_format($range['high']))
            ->assertDontSee(number_format(500000, 2));
    }

    public function test_the_page_says_a_model_produced_the_figure_and_when(): void
    {
        $property = $this->property();
        $valuation = $this->valuation($property);

        Livewire::test(PropertyValuationComponent::class, ['propertyId' => $property->id])
            ->call('viewValuation', $valuation->id)
            ->assertSee('Estimated by a model, not surveyed')
            ->assertSee($valuation->valuation_date->format('j M Y'));
    }

    public function test_the_page_states_what_the_estimate_was_derived_from(): void
    {
        $property = $this->property();
        $valuation = $this->valuation($property);

        Livewire::test(PropertyValuationComponent::class, ['propertyId' => $property->id])
            ->call('viewValuation', $valuation->id)
            ->assertSee('7')
            ->assertSee('Comparable sales')
            ->assertSee('Floor area')
            ->assertSee(number_format(1240));
    }

    public function test_a_visitor_can_book_a_valuation_with_a_person(): void
    {
        $property = $this->property();

        Livewire::test(PropertyValuationComponent::class, ['propertyId' => $property->id])
            ->assertSee('Book a valuation')
            ->assertSee(route('contact.show', ['property' => $property->id, 'interest' => 'selling']));
    }

    /**
     * viewValuation took any id and rendered it. On a public route that handed
     * every visitor the valuation history of any property by counting integers.
     */
    public function test_a_valuation_belonging_to_another_property_cannot_be_opened(): void
    {
        $property = $this->property();
        $other = $this->valuation($this->property());

        Livewire::test(PropertyValuationComponent::class, ['propertyId' => $property->id])
            ->call('viewValuation', $other->id)
            ->assertSet('valuation', null)
            ->assertSet('showReport', false);
    }
}
