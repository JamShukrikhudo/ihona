<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 03 of the Survey Sheet rollout: the Vellum / Night switch.
 */
class ThemeSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_switch_renders_on_a_public_page(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-theme-switch', $html);
        $this->assertStringContainsString('data-theme-set="light"', $html);
        $this->assertStringContainsString('data-theme-set="dark"', $html);
    }

    public function test_the_switch_is_named_for_assistive_technology(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // The icons carry no visible text, so each button needs its own name.
        $this->assertMatchesRegularExpression(
            '/data-theme-set="light"[^>]*aria-label="[^"]+"/s',
            $html
        );
        $this->assertMatchesRegularExpression(
            '/data-theme-set="dark"[^>]*aria-label="[^"]+"/s',
            $html
        );
        $this->assertStringContainsString('aria-pressed="false"', $html);
    }

    public function test_the_switch_script_reaches_the_page_through_the_stack(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Proves both the component's @push and the layout's @stack are wired.
        $this->assertStringContainsString("localStorage.setItem('theme'", $html);
        $this->assertStringContainsString('prefers-color-scheme: dark', $html);
    }

    public function test_the_switch_renders_once_per_page(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'data-theme-switch'));
        $this->assertSame(
            1,
            substr_count($html, "localStorage.setItem('theme'"),
            'the switch script is guarded by @once and must not repeat'
        );
    }

    public function test_the_footer_states_the_agency_and_its_contact_details(): void
    {
        $settings = app(\App\Settings\GeneralSettings::class);
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString($settings->site_name, $html);
        $this->assertStringContainsString($settings->site_email, $html);
        $this->assertStringContainsString('Issued by', $html);
    }
}
