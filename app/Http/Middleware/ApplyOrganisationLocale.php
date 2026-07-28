<?php

namespace App\Http\Middleware;

use App\Services\CurrentTeamResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApplyOrganisationLocale
{
    public function __construct(private readonly CurrentTeamResolver $teams) {}

    public function handle(Request $request, Closure $next): Response
    {
        $originalLocale = app()->getLocale();
        $originalTimezone = date_default_timezone_get();

        try {
            $profile = $this->teams->resolve($request->user())->organisationProfile;

            if ($profile?->isComplete()) {
                app()->setLocale($profile->language);
                date_default_timezone_set($profile->timezone);
            }
        } catch (Throwable) {
            // Setup and team-selection endpoints must remain reachable before onboarding.
        }

        try {
            return $next($request);
        } finally {
            app()->setLocale($originalLocale);
            date_default_timezone_set($originalTimezone);
        }
    }
}
