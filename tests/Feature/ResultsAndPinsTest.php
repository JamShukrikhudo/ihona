<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 20 of the Survey Sheet rollout: a result and its pin are the same home.
 *
 * The listings page shows a column of cards beside a map of the same homes, and
 * nothing connected the two. A reader who liked the look of the third card had
 * no way to find out where it was, and a reader who liked a pin's position had
 * no way to find out what it cost.
 *
 * The behaviour itself is a browser matter and is covered by
 * tests/Browser/map-pairing-check.mjs. What PHP can hold is the contract the
 * browser code depends on: that both sides carry the same identity, that the
 * pairing is bound by delegation rather than per-card listeners, and that it is
 * gated on the width where the map is actually on screen.
 */
class ResultsAndPinsTest extends TestCase
{
    use RefreshDatabase;

    private function mapScript(): string
    {
        return file_get_contents(resource_path('views/components/property-map.blade.php'));
    }

    public function test_a_card_carries_the_identity_its_pin_uses(): void
    {
        $property = Property::factory()->create([
            'status' => 'For Sale',
            'latitude' => 51.4543,
            'longitude' => -0.9781,
        ]);

        $this->get('/properties')
            ->assertOk()
            ->assertSee('data-property-id="'.$property->id.'"', false);
    }

    /**
     * The map's own points already carry the id — the pairing has nothing to
     * match on if that ever stops being true.
     */
    public function test_a_map_point_carries_the_same_identity(): void
    {
        $property = Property::factory()->create([
            'status' => 'For Sale',
            'latitude' => 51.4543,
            'longitude' => -0.9781,
        ]);

        $point = \App\View\Components\PropertyMap::points([$property])->first();

        $this->assertSame($property->id, $point['id']);
    }

    /**
     * A card is inside Livewire's DOM and is replaced wholesale on every filter
     * change, page change and debounced keystroke. A listener bound to the card
     * itself dies with it — silently, so the feature simply stops working after
     * the first filter. Delegation from a node Livewire never touches survives
     * all of it.
     */
    public function test_the_pairing_is_delegated_rather_than_bound_to_each_card(): void
    {
        $script = $this->mapScript();

        $this->assertMatchesRegularExpression(
            '/document\.addEventListener\(\s*[\'"](?:pointerover|mouseover)[\'"]/',
            $script,
            'card hover must be delegated from the document, not bound per card'
        );

        $this->assertMatchesRegularExpression(
            '/document\.addEventListener\(\s*[\'"]focusin[\'"]/',
            $script,
            'keyboard focus must raise the pin the same way hover does'
        );

        $this->assertStringContainsString(
            'closest(\'[data-property-id]\')',
            $script,
            'the delegated handler has to find the card the event came from'
        );
    }

    /**
     * Below 1024px the map is a collapsed <details> the reader has to open, so
     * raising a pin nobody can see is work done for nothing — and on a touch
     * screen a "hover" is a tap, which would fire on the way to the listing.
     */
    public function test_nothing_is_paired_below_the_width_where_the_map_is_open(): void
    {
        $this->assertStringContainsString(
            "matchMedia('(min-width: 1024px)')",
            $this->mapScript(),
            'the pairing must be gated on the width where the map holds its place'
        );
    }

    /**
     * Markers are destroyed and rebuilt on every filter change. A raised state
     * still pointing at a marker that has been removed leaves the card lit with
     * nothing to match it, and the next clear() reaches for a layer that is no
     * longer on the map.
     */
    public function test_the_raised_pair_is_released_before_the_markers_are_replaced(): void
    {
        $script = $this->mapScript();

        $update = strpos($script, "addEventListener('property-map-updated'");
        $this->assertNotFalse($update, 'the map no longer listens for a new set of points');

        $handler = substr($script, $update);
        $release = strpos($handler, 'clear()');
        $remove = strpos($handler, 'removeLayer');

        $this->assertNotFalse($release, 'the handler never releases the raised pair');
        $this->assertNotFalse($remove);
        $this->assertLessThan(
            $remove,
            $release,
            'the pair has to be released while the marker it points at still exists'
        );
    }

    /**
     * Tailwind scans these files as text and emits nothing for a class it
     * cannot see, so the raised state is a plain CSS rule on an attribute
     * rather than a utility toggled from JavaScript.
     */
    public function test_the_raised_state_is_styled_by_a_rule_that_ships(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('[data-raised]', $css);
        $this->assertStringContainsString('is-raised', $css);
    }
}
