<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 17 of the Survey Sheet rollout: model-generated material says so.
 *
 * A predicted return, a written description and a furnished room can all be
 * produced by a model, and all three arrive on the page looking exactly like a
 * surveyed fact. "AI-Powered Insights" in 12px grey at 2.6:1 was the only mark
 * on the page, and it read as a feature boast rather than a caveat.
 *
 * A figure a model produced carries a range, because a point estimate printed
 * to two decimal places claims a precision no model has.
 */
class ModelGeneratedLabellingTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $attributes = []): Property
    {
        return Property::factory()->create(array_merge([
            'status' => 'For Sale',
            'title' => 'Kendrick Road, Reading RG1',
        ], $attributes));
    }

    public function test_a_virtually_staged_photograph_says_so(): void
    {
        $property = $this->property();
        Image::create([
            'property_id' => $property->id,
            'team_id' => $property->team_id,
            'type' => 'image',
            'title' => 'Sitting room',
            'disk' => 'public',
            'file_path' => 'property-media/staged.jpg',
            'file_name' => 'staged.jpg',
            'is_public' => true,
            'is_staged' => true,
            'staging_style' => 'modern',
        ]);

        $item = $property->gallery()->first();

        $this->assertTrue($item->staged);

        $this->get('/properties/'.$property->id)->assertOk()->assertSee('Virtually staged');
    }

    public function test_a_photograph_of_the_actual_room_is_not_labelled(): void
    {
        $property = $this->property();
        Image::create([
            'property_id' => $property->id,
            'team_id' => $property->team_id,
            'type' => 'image',
            'title' => 'Sitting room',
            'disk' => 'public',
            'file_path' => 'property-media/real.jpg',
            'file_name' => 'real.jpg',
            'is_public' => true,
        ]);

        $this->get('/properties/'.$property->id)->assertOk()->assertDontSee('Virtually staged');
    }

    /**
     * The description field takes whatever it is given. Once a model can write
     * into it, the record has to remember that it did.
     */
    public function test_a_generated_description_says_so_and_dates_itself(): void
    {
        $property = $this->property([
            'description' => 'A handsome Victorian villa moments from the station.',
            'description_generated_at' => now()->subDays(3),
        ]);

        $this->get('/properties/'.$property->id)
            ->assertOk()
            ->assertSee('Written by a model')
            ->assertSee($property->description_generated_at->format('j M Y'));
    }

    public function test_a_description_an_agent_wrote_carries_no_label(): void
    {
        $property = $this->property([
            'description' => 'A handsome Victorian villa moments from the station.',
            'description_generated_at' => null,
        ]);

        $this->get('/properties/'.$property->id)->assertOk()->assertDontSee('Written by a model');
    }

    /**
     * A single number to two decimal places reads as a measurement. The band
     * widens with the risk score, which is the model's own statement of how
     * little it knows.
     */
    public function test_a_predicted_return_carries_a_range(): void
    {
        $service = app(\App\Services\AIInvestmentAnalysisService::class);

        $analysis = $service->analyzeInvestment($this->property(['price' => 450000]));

        $this->assertArrayHasKey('confidence', $analysis['prediction']);
        $this->assertLessThan(
            $analysis['prediction']['confidence']['high'],
            $analysis['prediction']['confidence']['low'],
            'the range has no width, so it claims certainty'
        );
        $this->assertLessThanOrEqual(
            $analysis['prediction']['predicted_roi'],
            $analysis['prediction']['confidence']['low']
        );
        $this->assertGreaterThanOrEqual(
            $analysis['prediction']['predicted_roi'],
            $analysis['prediction']['confidence']['high']
        );
    }

    public function test_the_range_widens_with_the_risk(): void
    {
        $service = app(\App\Services\AIInvestmentAnalysisService::class);

        $width = fn (array $p) => $p['confidence']['high'] - $p['confidence']['low'];

        $safe = $service->rangeFor(4.0, 1.0);
        $risky = $service->rangeFor(4.0, 9.0);

        $this->assertGreaterThan($width(['confidence' => $safe]), $width(['confidence' => $risky]));
    }

    public function test_the_investment_panel_names_what_produced_it(): void
    {
        $property = $this->property(['price' => 450000]);

        $page = $this->get('/properties/'.$property->id)->assertOk();

        $page->assertSee('Estimated by a model');
        $page->assertDontSee('AI-Powered Insights');
    }
}
