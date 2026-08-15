<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Every public page renders through one shell: site navigation and footer.
 *
 * Ticket 01 of the Survey Sheet rollout. Livewire full-page components resolve
 * their layout from config('livewire.component_layout'), which is easy to drift
 * away from the public shell one component at a time; these tests pin it.
 */
class PublicShellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Public routes that need no fixtures and no authentication.
     *
     * @return array<string, array{string}>
     */
    public static function publicRoutes(): array
    {
        return [
            'home' => ['/'],
            'property list' => ['/properties'],
            'property search' => ['/properties/search'],
            'about' => ['/about'],
            'services' => ['/services'],
            'contact' => ['/contact'],
            'terms' => ['/terms-and-conditions'],
            'privacy' => ['/privacy'],
            'calculators' => ['/calculators'],
            'news' => ['/news'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_route_renders_the_site_navigation(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertSee('id="navbar-cta"', false);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_route_renders_the_site_footer(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertSee('</footer>', false);
    }

    /**
     * A Livewire component view that @extends the layout renders the whole page
     * a second time inside the slot. Livewire throws on some of those and lets
     * others through silently, so assert the shell appears exactly once.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('publicRoutes')]
    public function test_public_route_renders_the_shell_exactly_once(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<html'), 'nested document');
        $this->assertSame(1, substr_count($html, 'id="navbar-cta"'), 'duplicated navigation');
        $this->assertSame(1, substr_count($html, '</footer>'), 'duplicated footer');
    }

    public function test_livewire_full_page_components_default_to_the_public_shell(): void
    {
        $this->assertSame(
            'layouts::app',
            config('livewire.component_layout'),
            'Livewire full-page components must default to the public shell, '
            .'otherwise a component without an explicit layout renders bare.'
        );
    }
}
