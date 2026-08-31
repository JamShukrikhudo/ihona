<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Liberu\Foundation\Settings\Services\ScopedSettings;
use Symfony\Component\HttpFoundation\Response;

final class ApplyTeamIntegrationSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $team = $user instanceof User ? $user->latestTeam : null;

        if ($team !== null) {
            $settings = (array) app(ScopedSettings::class)->resolve(
                'team.integrations',
                ['team' => $team->getKey()],
                [],
            );

            foreach ([
                'openai_api_key' => 'services.openai.api_key',
                'walkscore_api_key' => 'services.walkscore.api_key',
            ] as $teamKey => $configKey) {
                if (filled($settings[$teamKey] ?? null)) {
                    config([$configKey => $settings[$teamKey]]);
                }
            }
        }

        return $next($request);
    }
}
