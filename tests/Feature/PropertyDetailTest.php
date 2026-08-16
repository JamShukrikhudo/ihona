<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 07 of the Survey Sheet rollout: the property detail page.
 *
 * The page where a buyer or tenant decides whether to book a viewing, so
 * everything the record knows is disclosed and easy to find.
 */
class PropertyDetailTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();

        $this->property = Property::factory()->create([
            'title' => 'Alexandra Road, Reading RG1',
            'price' => 565000,
            'area_sqft' => 1240,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'year_built' => 1904,
            'status' => 'For Sale',
            'currency' => 'GBP',
            'epc' => ['rating' => 'B', 'score' => 84, 'assessment_date' => '2019-03-12'],
            'transit_score' => 78,
            'list_date' => now()->subDays(46),
        ]);
    }

    private function page(): string
    {
        return $this->get('/properties/'.$this->property->id)->assertOk()->getContent();
    }

    public function test_the_disclosure_panel_states_what_the_record_holds(): void
    {
        $html = $this->page();

        foreach (['Energy', 'Floor area', 'Built', 'Days listed'] as $label) {
            $this->assertStringContainsString($label, $html, "the panel is missing [{$label}]");
        }

        $this->assertStringContainsString('1,240', $html);
        $this->assertStringContainsString('1904', $html);
        $this->assertStringContainsString('46', $html);
        $this->assertStringContainsString('bg-epc-b', $html);
    }

    /**
     * A dated source outperforms any trust badge. "EPC — Register, 12 Mar 2019"
     * says where a number came from and how old it is.
     */
    public function test_a_disclosed_fact_says_where_it_came_from(): void
    {
        $html = $this->page();

        $this->assertMatchesRegularExpression('/Certificate|Register|Assessed/i', $html);
        $this->assertStringContainsString('2019', $html, 'the certificate date should be shown');
    }

    public function test_a_fact_the_record_lacks_says_so(): void
    {
        $this->property->update(['epc' => null, 'energy_rating' => null, 'energy_score' => null]);

        $html = $this->page();

        $this->assertStringContainsString('Not supplied', $html);
    }

    public function test_booking_a_viewing_is_reachable_from_the_first_screen_and_the_end(): void
    {
        $html = $this->page();

        $positions = [];
        $offset = 0;

        while (($found = strpos($html, 'Book a viewing', $offset)) !== false) {
            $positions[] = $found;
            $offset = $found + 1;
        }

        $this->assertGreaterThanOrEqual(
            2,
            count($positions),
            'a viewing must be bookable without scrolling back up'
        );
    }

    /**
     * Heavy media on the page that most needs to rank. A .glb model and a video
     * must not be fetched before the visitor asks for them.
     */
    public function test_heavy_media_never_loads_itself(): void
    {
        $view = file_get_contents(resource_path('views/livewire/property-detail.blade.php'));

        preg_match_all('/<model-viewer\b[^>]*/s', $view, $viewers);

        // model-viewer accepts only auto|manual for reveal. An invented value
        // silently disables every load gate, so the viewer stays a blank box —
        // asserting the string alone would lock that in, which it once did.
        $valid = ['auto', 'manual'];

        foreach ($viewers[0] as $viewer) {
            $this->assertStringContainsString('loading="lazy"', $viewer, 'a 3D model must wait to be asked for');

            preg_match('/reveal="([^"]*)"/', $viewer, $reveal);

            $this->assertNotEmpty($reveal, 'a 3D model needs an explicit reveal strategy');
            $this->assertContains(
                $reveal[1],
                $valid,
                "reveal=\"{$reveal[1]}\" is not a value model-viewer accepts (".implode('|', $valid).')'
            );
        }

        preg_match_all('/<video\b[^>]*/s', $view, $videos);

        $this->assertNotEmpty($videos[0]);

        foreach ($videos[0] as $video) {
            $this->assertStringContainsString('preload="none"', $video, 'a video must not preload');
        }
    }

    /**
     * The reveal strategy has to match the installed package, not a value that
     * merely sounds right. Read it from the type definition rather than a
     * literal, so an upgrade that changes the vocabulary fails here.
     */
    public function test_the_reveal_strategy_exists_in_the_installed_package(): void
    {
        $definition = base_path('node_modules/@google/model-viewer/lib/features/loading.d.ts');

        if (! file_exists($definition)) {
            $this->markTestSkipped('model-viewer is not installed');
        }

        preg_match(
            "/RevealAttributeValue\s*=\s*([^;]+);/",
            file_get_contents($definition),
            $declared
        );

        $this->assertNotEmpty($declared, 'could not read the reveal vocabulary');

        preg_match_all(
            '/reveal="([^"]*)"/',
            file_get_contents(resource_path('views/livewire/property-detail.blade.php')),
            $used
        );

        foreach ($used[1] as $value) {
            $this->assertStringContainsString(
                "'".$value."'",
                $declared[1],
                "reveal=\"{$value}\" is not declared by the installed model-viewer"
            );
        }
    }

    public function test_the_page_renders_for_a_rental(): void
    {
        $this->property->update(['status' => 'to_let', 'price' => 1150]);

        $html = $this->page();

        $this->assertStringContainsString('1,150', $html);
    }
}
