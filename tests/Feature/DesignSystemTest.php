<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * Ticket 04 of the Survey Sheet rollout: the styleguide and the shared
 * components that build it.
 *
 * The styleguide is rendered from the same components the public site uses, so
 * it cannot drift into a lie about what the site looks like.
 */
class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_styleguide_renders(): void
    {
        $this->get('/design')->assertOk()->assertSee('Survey Sheet');
    }

    public function test_the_styleguide_is_not_indexed(): void
    {
        $response = $this->get('/design')->assertOk();

        $this->assertStringContainsString('noindex', $response->headers->get('X-Robots-Tag') ?? '');
    }

    public function test_the_styleguide_shows_every_component(): void
    {
        $html = $this->get('/design')->assertOk()->getContent();

        foreach (['Buttons', 'Fields', 'Chips', 'Energy', 'Icons', 'Colour', 'Typography'] as $section) {
            $this->assertStringContainsString($section, $html);
        }
    }

    public function test_button_renders_a_link_when_given_an_href(): void
    {
        $html = Blade::render(
            '<x-ui.button href="/properties">Book a viewing</x-ui.button>'
        );

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href="/properties"', $html);
        $this->assertStringNotContainsString('<button', $html);
    }

    public function test_button_renders_a_button_without_an_href(): void
    {
        $html = Blade::render('<x-ui.button>Book a viewing</x-ui.button>');

        $this->assertStringContainsString('<button', $html);
        $this->assertStringContainsString('type="button"', $html);
    }

    public function test_button_variants_are_distinct(): void
    {
        $primary = Blade::render('<x-ui.button variant="primary">Go</x-ui.button>');
        $ghost = Blade::render('<x-ui.button variant="ghost">Go</x-ui.button>');

        $this->assertStringContainsString('bg-survey-500', $primary);
        $this->assertStringNotContainsString('bg-survey-500', $ghost);
    }

    /**
     * Two utilities for the same CSS property let the generated source order
     * decide the winner, not the order they are written in. The secondary
     * button lost its whole border to a base `border-transparent` this way.
     */
    public function test_no_button_variant_sets_a_border_colour_twice(): void
    {
        foreach (['primary', 'secondary', 'ghost', 'danger'] as $variant) {
            $html = Blade::render('<x-ui.button variant="'.$variant.'">Go</x-ui.button>');

            preg_match('/class="([^"]*)"/', $html, $m);
            $borderColours = array_filter(
                preg_split('/\s+/', $m[1] ?? ''),
                fn ($c) => preg_match('/^border-(?!\d)(?!\[)[a-z]/', $c)
            );

            $this->assertCount(
                1,
                $borderColours,
                "[{$variant}] sets border-color ".count($borderColours).' times: '
                .implode(', ', $borderColours)
            );
        }
    }

    public function test_the_secondary_button_has_a_visible_border(): void
    {
        $html = Blade::render('<x-ui.button variant="secondary">Save to shortlist</x-ui.button>');

        $this->assertStringContainsString('border-sheet-300', $html);
        $this->assertStringNotContainsString('border-transparent', $html);
    }

    public function test_a_disabled_button_cannot_be_activated(): void
    {
        $html = Blade::render('<x-ui.button disabled>Viewing booked</x-ui.button>');

        $this->assertStringContainsString('disabled', $html);
    }

    /**
     * Colour never carries meaning alone: the band letter is always rendered.
     */
    public function test_energy_band_always_states_its_letter(): void
    {
        foreach (range('A', 'G') as $band) {
            $html = Blade::render('<x-ui.epc-band band="'.$band.'" score="72" />');

            $this->assertStringContainsString('>'.$band.'<', $html);
            $this->assertStringContainsString('bg-epc-'.strtolower($band), $html);
        }
    }

    /**
     * Tailwind drops any @theme variable that no utility references, so a
     * component reaching for a token through an inline var() compiles to
     * nothing — the bands painted white on white. Colours must come from whole
     * class names the scanner can read as text.
     */
    public function test_energy_band_colours_survive_the_tailwind_scanner(): void
    {
        $source = file_get_contents(
            resource_path('views/components/ui/epc-band.blade.php')
        );

        foreach (range('a', 'g') as $band) {
            $this->assertStringContainsString(
                'bg-epc-'.$band,
                $source,
                'the band class must appear literally, not be built at runtime'
            );
        }

        $this->assertStringNotContainsString(
            'var(--color-epc-',
            Blade::render('<x-ui.epc-band band="A" score="92" />')
        );
    }

    public function test_energy_band_names_itself_for_screen_readers(): void
    {
        $html = Blade::render('<x-ui.epc-band band="B" score="84" />');

        $this->assertMatchesRegularExpression('/aria-label="[^"]*B[^"]*84[^"]*"/', $html);
    }

    public function test_an_unknown_energy_band_does_not_render_a_coloured_lie(): void
    {
        $html = Blade::render('<x-ui.epc-band band="Z" score="0" />');

        $this->assertStringNotContainsString("bg-epc-z", $html);
        $this->assertStringContainsString("Not supplied", $html);
    }

    /**
     * The styleguide's whole job is to show what the site actually renders. A
     * class assembled at runtime ("text-{$step}") is invisible to Tailwind's
     * scanner, so the step silently renders at the default size — a type scale
     * that lies about the type scale.
     */
    public function test_the_styleguide_never_builds_a_class_name_at_runtime(): void
    {
        $source = file_get_contents(resource_path('views/design/styleguide.blade.php'));

        foreach ([
            'text-mega', 'text-h1', 'text-h2', 'text-h3', 'text-h4', 'text-h5',
            'text-body-l', 'text-body', 'text-body-s', 'text-caption', 'text-micro',
        ] as $utility) {
            $this->assertStringContainsString(
                $utility,
                $source,
                "[{$utility}] must appear literally for Tailwind to generate it"
            );
        }

        $this->assertDoesNotMatchRegularExpression(
            '/class="[^"]*\b[a-z-]+-\{\{/',
            $source,
            'a utility prefix followed by an interpolation never reaches the stylesheet'
        );
    }

    public function test_field_wires_its_label_hint_and_error_to_the_control(): void
    {
        $html = Blade::render(
            '<x-ui.field id="email" label="Email" hint="We confirm here" error="Add the part after the @">'
            .'<input id="email" type="email">'
            .'</x-ui.field>'
        );

        $this->assertStringContainsString('for="email"', $html);
        $this->assertStringContainsString('Add the part after the @', $html);
        $this->assertStringContainsString('id="email-error"', $html);
        $this->assertStringContainsString('id="email-hint"', $html);
    }

    public function test_chip_states_its_tone_and_its_words(): void
    {
        $html = Blade::render('<x-ui.chip tone="verified">Chain-free</x-ui.chip>');

        $this->assertStringContainsString('Chain-free', $html);
        $this->assertStringContainsString('verdigris', $html);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function iconNames(): array
    {
        return array_map(
            fn ($name) => [$name],
            array_combine(
                $names = [
                    'bedrooms', 'bathrooms', 'floor-area', 'floor-plan', 'aspect',
                    'certificate', 'epc', 'transport', 'location', 'chain',
                    'viewing', 'price', 'tenure', 'tour', 'property', 'enquiry',
                ],
                $names
            )
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('iconNames')]
    public function test_icon_renders_on_the_technical_pen_geometry(string $name): void
    {
        $html = Blade::render('<x-ui.icon name="'.$name.'" />');

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('stroke-width="1.5"', $html);
        $this->assertStringContainsString('stroke-linecap="square"', $html);
        $this->assertStringContainsString('viewBox="0 0 24 24"', $html);
        $this->assertMatchesRegularExpression('/<path/', $html);
    }

    public function test_an_icon_without_a_label_is_hidden_from_screen_readers(): void
    {
        $bare = Blade::render('<x-ui.icon name="bedrooms" />');
        $named = Blade::render('<x-ui.icon name="bedrooms" label="Bedrooms" />');

        $this->assertStringContainsString('aria-hidden="true"', $bare);
        $this->assertStringContainsString('role="img"', $named);
        $this->assertStringContainsString('Bedrooms', $named);
    }

    /**
     * A silently missing icon is a hole in the interface nobody notices until a
     * user reports a blank button. Blade wraps the throw in a ViewException, so
     * assert on the message and the named alternatives instead of the class.
     */
    public function test_an_unknown_icon_fails_loudly(): void
    {
        try {
            Blade::render('<x-ui.icon name="unicorn" />');
            $this->fail('an unknown icon should not render silently');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Unknown icon [unicorn]', $e->getMessage());
            $this->assertStringContainsString('bedrooms', $e->getMessage());
        }
    }

    public function test_the_home_page_no_longer_references_font_awesome(): void
    {
        $home = file_get_contents(resource_path('views/home.blade.php'));

        $this->assertStringNotContainsString('fa-', $home);
        $this->assertStringNotContainsString('fas ', $home);
    }
}
