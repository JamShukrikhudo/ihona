<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * The sign-in journey. Four of these seven views referenced <x-guest-layout>,
 * which this application does not have, and threw on render.
 */
class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    /** Pages a signed-out visitor can reach by URL. */
    public static function guestPages(): array
    {
        return [
            'login' => ['/login'],
            'register' => ['/register'],
            'forgot password' => ['/forgot-password'],
            'reset password' => ['/reset-password/a-token'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('guestPages')]
    public function test_a_signed_out_visitor_can_open_it(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('guestPages')]
    public function test_it_carries_the_site_chrome_and_the_design_system(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertStringContainsString('id="navbar-cta"', $html, 'the page lost the site navigation');
        $this->assertStringContainsString('</footer>', $html);
        $this->assertStringContainsString('bg-sheet-000', $html, 'the panel is not on the design system');
    }

    /** Source rather than rendered page: the shared chrome still has its own. */
    public function test_no_sign_in_screen_is_still_on_the_old_utilities(): void
    {
        $files = array_merge(
            glob(resource_path('views/auth/*.blade.php')),
            [
                resource_path('views/components/auth-panel.blade.php'),
                resource_path('views/components/ui/control.blade.php'),
                resource_path('views/components/socialstream.blade.php'),
            ],
        );

        $offenders = [];

        foreach ($files as $file) {
            // Only class attributes: a comment explaining what the old class was
            // is not the old class.
            preg_match_all('~class="([^"]*)"~', file_get_contents($file), $matches);
            $classes = implode(' ', $matches[1]);

            foreach (['bg-white', 'text-gray-', 'bg-gray-', 'border-gray-', 'text-red-', 'indigo'] as $legacy) {
                if (str_contains($classes, $legacy)) {
                    $offenders[] = basename($file).' still uses '.$legacy;
                }
            }
        }

        $this->assertSame([], $offenders, implode("\n", $offenders));
    }

    /** Only reachable mid-journey, so rendered rather than fetched. */
    public static function journeyViews(): array
    {
        return [
            'two factor challenge' => ['auth.two-factor-challenge'],
            'verify email' => ['auth.verify-email'],
            'confirm password' => ['auth.confirm-password'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('journeyViews')]
    public function test_it_renders(string $view): void
    {
        $this->actingAs(User::factory()->create());

        // Rendered rather than fetched, so the error bag the HTTP middleware
        // would have shared has to be supplied by hand.
        View::share('errors', new \Illuminate\Support\ViewErrorBag);

        $html = View::make($view)->render();

        $this->assertStringContainsString('bg-sheet-000', $html);
    }

    public function test_the_reset_form_carries_the_token_and_the_address(): void
    {
        $html = $this->get('/reset-password/a-token?email=someone@example.com')->assertOk()->getContent();

        $this->assertStringContainsString('value="a-token"', $html);
        $this->assertStringContainsString('someone@example.com', $html);
    }

    public function test_a_failed_sign_in_says_what_to_do_about_it(): void
    {
        $this->from('/login')
            ->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong-password'])
            ->assertRedirect('/login');

        $html = $this->get('/login')->assertOk()->getContent();

        $this->assertStringContainsString('text-fault', $html, 'the error is not shown in the fault tone');
    }

    public function test_register_still_asks_which_kind_of_visitor_this_is(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();

        foreach (['tenant', 'buyer', 'seller', 'landlord', 'contractor'] as $role) {
            $this->assertStringContainsString('value="'.$role.'"', $html);
        }
    }

    /** A provider with no client id throws on redirect. */
    public function test_only_a_provider_that_can_finish_a_sign_in_is_offered(): void
    {
        config(['services.google' => ['client_id' => 'id', 'client_secret' => 'secret', 'redirect' => '/']]);

        $html = $this->get('/login')->assertOk()->getContent();

        $this->assertStringContainsString('Google', $html);
        $this->assertStringNotContainsString('Bitbucket', $html, 'a provider with no credentials is not a way in');
    }

    public function test_with_nothing_configured_no_other_way_in_is_offered(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();

        $this->assertStringNotContainsString('Or Login Via', $html);
    }
}
