<?php

namespace Tests\Feature;

use App\Livewire\NeighborhoodReviewForm;
use App\Livewire\PropertyReviewForm;
use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * "Share Your Experience" and "Review this neighbourhood" were the same form
 * written twice, and the copies had drifted: one submitted in indigo, the other
 * in the old primary ramp, both used grey labels and red error text, and the
 * five stars were an unnamed glyph with `focus:outline-none` on them — a screen
 * reader announced "button, button, button" and a keyboard saw nothing.
 *
 * Both now render <x-review-form>. They also both used id="title", and both
 * appear on the property page, so the second label pointed at the first input.
 */
class PropertyReviewFormTest extends TestCase
{
    use RefreshDatabase;

    private function propertyForm(): string
    {
        return Livewire::actingAs(User::factory()->create())
            ->test(PropertyReviewForm::class, ['propertyId' => Property::factory()->create()->id])
            ->html();
    }

    private function neighbourhoodForm(): string
    {
        return Livewire::actingAs(User::factory()->create())
            ->test(NeighborhoodReviewForm::class, ['neighborhoodId' => Neighborhood::factory()->create()->id])
            ->html();
    }

    public function test_the_form_is_built_from_the_shared_controls(): void
    {
        $html = $this->propertyForm();

        // The control primitive's own treatment, on the input and the textarea.
        $this->assertSame(2, substr_count($html, 'rounded-sheet border border-sheet-300 bg-sheet-000 px-3.5'));
        $this->assertStringContainsString('inline-flex items-center justify-center gap-2', $html);
    }

    public function test_no_control_wears_the_old_palette(): void
    {
        foreach ([$this->propertyForm(), $this->neighbourhoodForm()] as $html) {
            foreach (['indigo', 'text-gray-', 'border-gray-', 'text-red-', 'bg-green-100', 'text-yellow-400'] as $stale) {
                $this->assertStringNotContainsString($stale, $html);
            }
        }
    }

    public function test_every_star_says_what_it_sets(): void
    {
        $html = $this->propertyForm();

        for ($i = 1; $i <= 5; $i++) {
            $this->assertStringContainsString('aria-label="'.$i.' out of 5"', $html);
        }

        // The default is 5, so all five read as set.
        $this->assertSame(5, substr_count($html, 'aria-pressed="true"'));

        // Only the stars: the shared control clears the browser ring on purpose,
        // because app.css draws one for every focusable element instead.
        preg_match_all('/<button[^>]*aria-pressed[^>]*>/', $html, $stars);
        $this->assertCount(5, $stars[0]);
        $this->assertStringNotContainsString('focus:outline-none', implode('', $stars[0]));
    }

    public function test_the_two_forms_do_not_share_an_id(): void
    {
        $ids = fn (string $html) => preg_match_all('/ id="([^"]+)"/', $html, $m) ? $m[1] : [];

        $property = $ids($this->propertyForm());
        $neighbourhood = $ids($this->neighbourhoodForm());

        $this->assertNotEmpty($property);
        $this->assertSame([], array_intersect($property, $neighbourhood));
    }
}
