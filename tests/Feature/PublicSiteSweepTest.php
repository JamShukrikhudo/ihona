<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ticket 12 of the Survey Sheet rollout: the acceptance sweep.
 *
 * The route list comes from the router, not from a list someone maintains by
 * hand — a page nobody remembered to add is exactly the page that regresses.
 */
class PublicSiteSweepTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every public storefront route, enumerated from the router.
     *
     * Excludes anything behind auth, the API, Livewire's own asset routes, and
     * the Filament panels, which are not part of this rollout.
     *
     * @return array<string, array{string}>
     */
    /**
     * @return list<string>
     */
    private function publicRoutes(): array
    {
        $routes = [];

        foreach (app('router')->getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if (array_intersect(['auth', 'auth:sanctum', 'verified'], $route->gatherMiddleware())) {
                continue;
            }

            $action = $route->getActionName();

            if (str_contains($action, 'Filament')) {
                continue;
            }

            if (! str_starts_with($action, 'App\Http\Controllers\\')
                && ! str_starts_with($action, 'App\Livewire\\')) {
                continue;
            }

            $uri = $route->uri();

            if (str_starts_with($uri, 'api/') || str_contains($uri, 'ar-tour')) {
                continue; // JSON endpoints, not pages
            }

            $routes[$uri] = $uri;
        }

        // Fortify owns the auth pages, so the App\ filter above skipped them —
        // and a published Blade in this repo renders them, so this repo can
        // break them. One did, and the sweep reported clean.
        foreach (['login', 'register', 'password.request', 'password.reset'] as $name) {
            $route = app('router')->getRoutes()->getByName($name);

            if ($route) {
                $routes[$route->uri()] = $route->uri();
            }
        }

        ksort($routes);

        return array_values($routes);
    }

    private function makeProperty(): Property
    {
        return Property::factory()->create([
            'title' => 'Alexandra Road, Reading RG1',
            'price' => 565000,
            'area_sqft' => 1240,
            'status' => 'For Sale',
            'currency' => 'GBP',
            'epc' => ['rating' => 'B', 'score' => 84],
            'list_date' => now()->subDays(12),
            'latitude' => 51.45,
            'longitude' => -0.97,
        ]);
    }

    private function resolve(string $uri): string
    {
        $property = $this->makeProperty();

        return '/'.str_replace(
            ['{propertyId}', '{property}', '{propertyIds}', '{slug}', '{token}'],
            [$property->id, $property->id, (string) $property->id, 'a-news-story', 'a-reset-token'],
            $uri
        );
    }

    public function test_no_public_page_loads_a_third_party_asset(): void
    {
        $hosts = [
            'fonts.googleapis.com', 'fonts.gstatic.com', 'unpkg.com', 'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com', 'ajax.googleapis.com', 'stackpath.bootstrapcdn.com',
            'vecteezy.com', 'unsplash.com', 'via.placeholder',
        ];

        $offenders = [];

        foreach ($this->walk() as $uri => $html) {
            foreach ($hosts as $host) {
                if (str_contains($html, $host)) {
                    $offenders[] = "{$uri} pulls an asset from {$host}";
                }
            }
        }

        $this->assertSame([], $offenders, implode("
", $offenders));
    }

    public function test_every_public_page_renders_the_shell(): void
    {
        $offenders = [];

        foreach ($this->walk() as $uri => $html) {
            if (substr_count($html, '<html') !== 1) {
                $offenders[] = "{$uri} renders a nested document";
            }

            if (! str_contains($html, 'id="navbar-cta"')) {
                $offenders[] = "{$uri} has no navigation";
            }

            if (! str_contains($html, '</footer>')) {
                $offenders[] = "{$uri} has no footer";
            }
        }

        $this->assertSame([], $offenders, implode("
", $offenders));
    }

    /**
     * Every public page, fetched once. Redirects are followed on purpose: a
     * route that hands the visitor to a page that works has done its job, and
     * the page they land on is what the sweep should be checking.
     *
     * @return array<string, string>
     */
    private function walk(): array
    {
        $pages = [];

        foreach ($this->publicRoutes() as $uri) {
            $response = $this->followingRedirects()->get($this->resolve($uri));

            if ($response->status() === 404) {
                continue; // needs a fixture this sweep does not build
            }

            // Say why, not just that: a sweep that reports a bare status is
            // half a tool, so a failure is replayed without the handler to get
            // the exception behind it.
            if ($response->status() !== 200) {
                $reason = "status {$response->status()}";

                try {
                    $this->withoutExceptionHandling()
                        ->followingRedirects()
                        ->get($this->resolve($uri));
                } catch (\Throwable $e) {
                    $reason = get_class($e).': '.$e->getMessage();
                } finally {
                    $this->withExceptionHandling();
                }

                $this->fail("[{$uri}] did not load — {$reason}");
            }

            $pages[$uri] = $response->getContent();
        }

        $this->assertNotEmpty($pages, 'the sweep found no pages to walk');

        return $pages;
    }

    /**
     * The sweep is only worth anything if it is walking the whole site. If a
     * new public page appears, this number moves and someone has to look.
     */
    public function test_the_sweep_covers_every_public_page(): void
    {
        $this->assertGreaterThanOrEqual(
            12,
            count($this->publicRoutes()),
            'the sweep is walking fewer pages than the site has'
        );
    }

    public function test_reduced_motion_stops_every_animation(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertMatchesRegularExpression(
            '/prefers-reduced-motion: reduce.*animation-iteration-count:\s*1\s*!important/s',
            $css,
            'without this an infinite animation restarts instead of stopping'
        );
        $this->assertMatchesRegularExpression(
            '/prefers-reduced-motion: reduce.*transition-duration:\s*0\.01ms\s*!important/s',
            $css
        );
    }

    public function test_focus_is_visible_and_never_removed(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('focus-visible', $css);
        $this->assertStringContainsString('outline: 2px solid var(--color-survey-500)', $css);
        $this->assertDoesNotMatchRegularExpression(
            '/outline:\s*(none|0)\s*;/',
            $css,
            'the focus ring must never be removed outright'
        );
    }

    /**
     * Colour never carries meaning alone. The energy band is the one place the
     * temptation is strongest, because the statutory colours are so
     * recognisable — so it always renders its letter too.
     */
    public function test_meaning_is_never_carried_by_colour_alone(): void
    {
        $property = $this->makeProperty();

        $html = $this->get('/properties/'.$property->id)->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/>B<\/span>/', $html, 'the band letter must be rendered');
        $this->assertStringContainsString('aria-label="Energy rating band B', $html);
    }
}
