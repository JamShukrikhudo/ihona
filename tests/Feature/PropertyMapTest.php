<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * The map is the heaviest thing on any page that carries it, and the only place
 * staff-entered text reaches a script.
 */
class PropertyMapTest extends TestCase
{
    use RefreshDatabase;

    private function mapped(array $attributes = []): Property
    {
        return Property::factory()->create(array_merge([
            'latitude' => 51.45,
            'longitude' => -0.97,
            'price' => 565000,
            'title' => 'Alexandra Road',
        ], $attributes));
    }

    /**
     * The class component used to discard what the caller passed and re-query
     * without `price`, so every marker popup read "NaN".
     */
    public function test_the_component_uses_the_properties_it_is_given(): void
    {
        $this->mapped(['title' => 'Queried row']);

        $html = Blade::render(
            '<x-property-map :properties="$p" />',
            ['p' => collect([
                ['id' => 1, 'title' => 'Passed row', 'price' => 1234, 'latitude' => 51.0, 'longitude' => -1.0],
            ])]
        );

        $this->assertStringContainsString('Passed row', $html);
        $this->assertStringNotContainsString('Queried row', $html);
    }

    public function test_a_marker_carries_a_price(): void
    {
        $this->mapped();

        $html = Blade::render('<x-property-map />');

        $this->assertStringContainsString('565000', $html, 'the popup has nothing to format without a price');
    }

    /**
     * Titles are staff-entered across tenants. Concatenated into popup HTML
     * they are stored XSS for every visitor who opens the marker.
     */
    public function test_a_title_never_reaches_the_popup_as_html(): void
    {
        $this->mapped(['title' => '<img src=x onerror=alert(1)>']);

        // Through a real page: the init script arrives via @stack('scripts'),
        // which an isolated Blade::render never reaches.
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('<img src=x onerror', $html, 'the title must never render as markup');
        $this->assertStringContainsString('textContent', $html, 'the popup must be built as nodes');
        $this->assertStringNotContainsString("'<strong>' + property.title", $html);
    }

    /**
     * Memoising the default query stops a Livewire re-render paying for it on
     * every keystroke — but a static would outlive the request under Octane and
     * outlive the test that populated it, serving a stale map either way.
     */
    public function test_the_memoised_map_does_not_outlive_the_request(): void
    {
        $key = \App\View\Components\PropertyMap::class.':defaults';

        // Static methods are fine; a static *property* is the thing that would
        // hold the memo past the end of the request.
        $this->assertDoesNotMatchRegularExpression(
            '/\b(private|protected|public)\s+static\s+[^(]*\$/',
            file_get_contents(app_path('View/Components/PropertyMap.php')),
            'a static property memo outlives the request'
        );

        $this->mapped(['title' => 'First render']);
        $this->assertStringContainsString('First render', Blade::render('<x-property-map />'));
        $this->assertTrue(app()->bound($key), 'the memo should live in the container');

        // What the next request does: the container is rebuilt, so the memo goes.
        app()->forgetInstance($key);
        $this->mapped(['title' => 'Added later']);

        $this->assertStringContainsString('Added later', Blade::render('<x-property-map />'));
    }

    public function test_the_default_query_is_bounded(): void
    {
        $this->assertStringContainsString(
            'limit(500)',
            file_get_contents(app_path('View/Components/PropertyMap.php')),
            'an unbounded map query loads every property on the platform'
        );
    }

    /**
     * A Livewire re-render morphs the container and strips Leaflet's panes; the
     * @once init script has already disconnected, so nothing rebuilds it.
     */
    public function test_the_map_container_survives_a_livewire_render(): void
    {
        $html = Blade::render('<x-property-map />');

        $this->assertMatchesRegularExpression('/<div[^>]*wire:ignore/', $html);
    }

    public function test_more_than_one_map_can_initialise(): void
    {
        $markup = Blade::render('<x-property-map /><x-property-map />');
        $this->assertSame(2, substr_count($markup, 'data-properties='), 'both maps should render');

        $page = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('querySelectorAll', $page);
        $this->assertStringNotContainsString(
            "document.querySelector('[data-map]')",
            $page,
            'a single querySelector leaves every map after the first as a grey box'
        );
    }
}
