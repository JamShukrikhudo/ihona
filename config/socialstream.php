<?php

use JoelButcher\Socialstream\Features;
use JoelButcher\Socialstream\Providers;

return [
    'guard' => 'web', // used if Fortify is not installed
    'middleware' => ['web'],
    'prompt' => 'Or Login Via',
    // Google is wired to a real OAuth app (GOOGLE_CLIENT_ID/SECRET in .env).
    // GitHub/GitLab/Bitbucket/Slack/LinkedIn/Twitter are developer-network
    // providers with no relevance to this market's real estate consumers,
    // so they stay out. Add back only a provider that's actually configured.
    'providers' => [
        Providers::google(),
    ],
    'features' => [
        // Features::generateMissingEmails(),
        Features::createAccountOnFirstLogin(),
        // Features::globalLogin(),
        // Features::authExistingUnlinkedUsers(),
        Features::rememberSession(),
        Features::providerAvatars(),
        Features::refreshOAuthTokens(),
    ],
    'home' => '/dashboard',
    'redirects' => [
        'login' => '/dashboard',
        'register' => '/dashboard',
        'login-failed' => '/login',
        'registration-failed' => '/register',
        'provider-linked' => '/user/profile',
        'provider-link-failed' => '/user/profile',
    ],
];
