<?php

namespace Liberu\Foundation\Identity\Socialstream\Actions;

use JoelButcher\Socialstream\Contracts\GeneratesProviderRedirect;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GenerateRedirectForProvider implements GeneratesProviderRedirect
{
    /**
     * Generates the redirect for a given provider.
     */
    public function generate(string $provider): RedirectResponse
    {
        // Carries the SPA's intent across the external OAuth roundtrip —
        // see SpaAwareOAuthResponse, which reads this once the callback lands.
        if (request()->boolean('spa')) {
            session(['socialstream.spa_redirect' => true]);
        }

        return Socialite::driver($provider)->redirect();
    }
}
