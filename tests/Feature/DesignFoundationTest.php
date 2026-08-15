<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 02 of the Survey Sheet rollout: the design foundation.
 *
 * The stylesheet assertions read the source rather than the build, so they hold
 * whether or not assets have been compiled in the test environment.
 */
class DesignFoundationTest extends TestCase
{
    use RefreshDatabase;

    private function stylesheet(): string
    {
        return file_get_contents(resource_path('css/app.css'));
    }

    public function test_public_pages_request_no_third_party_font(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('fonts.googleapis.com', $html);
        $this->assertStringNotContainsString('fonts.gstatic.com', $html);
        $this->assertStringNotContainsString('fonts.googleapis.com', $this->stylesheet());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function selfHostedFonts(): array
    {
        return [
            'display' => ['archivo-variable.woff2'],
            'body' => ['instrument-sans-variable.woff2'],
            'mono 400' => ['ibm-plex-mono-400.woff2'],
            'mono 500' => ['ibm-plex-mono-500.woff2'],
            'mono 600' => ['ibm-plex-mono-600.woff2'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('selfHostedFonts')]
    public function test_font_file_is_self_hosted_and_declared(string $file): void
    {
        $this->assertFileExists(public_path('fonts/'.$file));
        $this->assertStringContainsString('/fonts/'.$file, $this->stylesheet());
    }

    public function test_the_three_type_roles_are_declared(): void
    {
        $css = $this->stylesheet();

        $this->assertMatchesRegularExpression("/--font-display:\s*'Archivo'/", $css);
        $this->assertMatchesRegularExpression("/--font-sans:\s*'Instrument Sans'/", $css);
        $this->assertMatchesRegularExpression("/--font-mono:\s*'IBM Plex Mono'/", $css);
    }

    /**
     * Both themes must come from one definition per token. A second, hand
     * maintained dark palette is exactly how the two sides drift apart.
     */
    public function test_theme_colours_are_defined_once_via_light_dark(): void
    {
        $css = $this->stylesheet();

        $this->assertStringContainsString('color-scheme: light dark', $css);
        $this->assertStringContainsString("[data-theme='dark']", $css);
        $this->assertStringContainsString("[data-theme='light']", $css);
        $this->assertGreaterThan(20, substr_count($css, 'light-dark('));
    }

    public function test_ground_and_ink_come_from_tokens(): void
    {
        $css = $this->stylesheet();

        $this->assertMatchesRegularExpression(
            '/\bbody\s*\{[^}]*background:\s*var\(--color-sheet-100\)/s',
            $css,
            'body needs an explicit token ground'
        );
        $this->assertStringNotContainsString('bg-gray-100', file_get_contents(
            resource_path('views/layouts/app.blade.php')
        ));
    }

    public function test_reduced_motion_and_focus_ring_are_set_globally(): void
    {
        $css = $this->stylesheet();

        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertStringContainsString('focus-visible', $css);
        $this->assertStringContainsString('outline: 2px solid var(--color-survey-500)', $css);
    }

    public function test_the_shell_stamps_a_stored_theme_before_paint(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString("localStorage.getItem('theme')", $html);
        $this->assertStringContainsString('documentElement.dataset.theme', $html);

        $head = substr($html, 0, strpos($html, '</head>'));
        $this->assertStringContainsString(
            'documentElement.dataset.theme',
            $head,
            'the stamp must run in <head>, otherwise the page flashes the wrong ground'
        );
    }

    public function test_the_shell_renders_pushed_scripts_and_styles(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString("@stack('scripts')", $layout);
        $this->assertStringContainsString("@stack('styles')", $layout);
        $this->assertStringContainsString("@yield('styles')", $layout);
    }
}
