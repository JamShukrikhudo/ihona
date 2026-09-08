<?php

declare(strict_types=1);

namespace Liberu\Foundation\Identity\Socialstream\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use JoelButcher\Socialstream\Contracts\AuthenticatesOAuthCallback;
use JoelButcher\Socialstream\Contracts\SocialstreamResponse;
use Laravel\Socialite\Two\User as SocialiteUser;
use Liberu\Foundation\Identity\Socialstream\Actions\VerifyTelegramAuthPayload;

/**
 * Telegram isn't a Socialite driver, so it can't use Socialstream's generic
 * /oauth/{provider}/callback route — this verifies Telegram's own signed
 * redirect payload, then hands off to the same
 * AuthenticatesOAuthCallback::authenticate() pipeline every other provider
 * uses (find-or-link-or-register, Fortify login), rather than reimplementing
 * user creation for one more provider.
 */
class TelegramAuthController
{
    public function callback(Request $request, AuthenticatesOAuthCallback $authenticator): SocialstreamResponse|RedirectResponse
    {
        $botToken = (string) config('services.telegram.bot_token');

        $payload = (new VerifyTelegramAuthPayload($botToken))->verify($request);

        abort_if($payload === null, 403, 'Invalid Telegram login payload.');

        $name = trim(($payload['first_name'] ?? '').' '.($payload['last_name'] ?? ''));
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        $providerUser = (new SocialiteUser())->map([
            'id' => (string) $payload['id'],
            'nickname' => $payload['username'] ?? null,
            'name' => $name !== '' ? $name : 'Telegram User '.$payload['id'],
            // Telegram never shares an email; synthesize a stable, unique
            // placeholder since users.email is NOT NULL + unique.
            'email' => "telegram-{$payload['id']}@users.{$host}",
            'avatar' => $payload['photo_url'] ?? null,
        ])->setToken('telegram');

        return $authenticator->authenticate('telegram', $providerUser);
    }
}
