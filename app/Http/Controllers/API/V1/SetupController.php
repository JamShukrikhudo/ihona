<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\OrganisationProfile;
use App\Services\CountryConfiguration;
use App\Services\CurrentTeamResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SetupController extends Controller
{
    public function __construct(
        private readonly CurrentTeamResolver $teams,
        private readonly CountryConfiguration $countries,
    ) {}

    public function options(): JsonResponse
    {
        return response()->json(['data' => $this->countries->all()]);
    }

    public function status(Request $request): JsonResponse
    {
        $team = $this->teams->resolve($request->user());
        $profile = $team->organisationProfile;

        return response()->json([
            'data' => [
                'complete' => $profile?->isComplete() ?? false,
                'profile' => $profile,
            ],
        ]);
    }

    public function complete(Request $request): JsonResponse
    {
        $team = $this->teams->resolve($request->user());
        $membershipRole = $team->users()->whereKey($request->user()->id)->first()?->membership?->role;

        abort_unless(
            $team->user_id === $request->user()->id || in_array($membershipRole, ['owner', 'admin'], true),
            403,
            'Only organisation owners and administrators can complete setup.',
        );

        $data = $request->validate($this->rules());
        $primaryCountry = strtoupper($data['primary_country']);
        $operatingCountries = array_values(array_unique(array_map('strtoupper', $data['operating_countries'])));

        if (! in_array($primaryCountry, $operatingCountries, true)) {
            return response()->json([
                'message' => 'The primary country must be one of the operating countries.',
                'errors' => ['primary_country' => ['Select a primary operating country.']],
            ], 422);
        }

        $defaults = $this->countries->defaults($primaryCountry);
        $attributes = [
            ...$data,
            ...$defaults,
            'operating_countries' => $operatingCountries,
            'primary_country' => $primaryCountry,
            'setup_completed_at' => now(),
        ];

        $profile = DB::transaction(function () use ($team, $attributes) {
            $profile = OrganisationProfile::updateOrCreate(
                ['team_id' => $team->id],
                $attributes,
            );
            $team->update(['name' => $attributes['agency_name']]);

            return $profile;
        });

        return response()->json(['data' => $profile->fresh()], 201);
    }

    private function rules(): array
    {
        $countryCodes = array_keys($this->countries->all());

        return [
            'agency_name' => ['required', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:2048'],
            'branding' => ['nullable', 'array'],
            'branding.primary_color' => ['nullable', 'hex_color'],
            'branding.secondary_color' => ['nullable', 'hex_color'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'array'],
            'address.line_1' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:100'],
            'address.region' => ['nullable', 'string', 'max:100'],
            'address.postal_code' => ['required', 'string', 'max:20'],
            'operating_countries' => ['required', 'array', 'min:1'],
            'operating_countries.*' => ['required', 'string', Rule::in($countryCodes)],
            'primary_country' => ['required', 'string', Rule::in($countryCodes)],
        ];
    }
}
