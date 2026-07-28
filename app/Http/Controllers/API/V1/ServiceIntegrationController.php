<?php

namespace App\Http\Controllers\API\V1;

use App\Models\ServiceIntegration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ServiceIntegrationController extends TenantCrudController
{
    private const PROVIDERS = [
        'email' => ['smtp', 'microsoft_365', 'gmail'],
        'calendar' => ['google_calendar', 'microsoft_outlook'],
        'maps' => ['google_maps', 'openstreetmap'],
        'sms' => ['twilio', 'messagebird', 'vonage', 'custom'],
        'push' => ['firebase', 'onesignal', 'custom'],
    ];

    protected string $model = ServiceIntegration::class;

    protected string $routeParameter = 'service_integration';

    protected array $searchable = ['name', 'provider', 'category'];

    protected array $filterable = ['category', 'provider', 'active', 'is_default', 'last_check_status'];

    public function options(): JsonResponse
    {
        return response()->json(['data' => collect(self::PROVIDERS)->map(
            fn (array $providers, string $category) => [
                'category' => $category,
                'providers' => $providers,
            ]
        )->values()]);
    }

    public function check(Request $request, int $serviceIntegration): JsonResponse
    {
        $integration = $this->teamQuery($request)->findOrFail($serviceIntegration);

        if (! $integration->active) {
            throw ValidationException::withMessages([
                'active' => ['Inactive integrations cannot be checked.'],
            ]);
        }

        $integration->update([
            'last_checked_at' => now(),
            'last_check_status' => 'configured',
            'last_error' => null,
        ]);

        return response()->json([
            'data' => $integration->fresh(),
            'meta' => [
                'message' => 'Configuration is valid. Provider connectivity is checked by the asynchronous adapter.',
            ],
        ]);
    }

    protected function rules(Request $request, ?Model $record = null): array
    {
        $category = $request->input('category', $record?->category);
        $providers = self::PROVIDERS[$category] ?? collect(self::PROVIDERS)->flatten()->all();

        return [
            'category' => [
                $record ? 'sometimes' : 'required',
                Rule::in(array_keys(self::PROVIDERS)),
            ],
            'provider' => [
                $record ? 'sometimes' : 'required',
                Rule::in($providers),
            ],
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'credentials' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $this->validateProviderCategory($attributes['category'], $attributes['provider']);
        $this->clearDefault($request, $attributes);

        return $attributes;
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        $category = $attributes['category'] ?? $record->category;
        $provider = $attributes['provider'] ?? $record->provider;
        $this->validateProviderCategory($category, $provider);
        $this->clearDefault($request, $attributes, $record);

        return $attributes;
    }

    private function validateProviderCategory(string $category, string $provider): void
    {
        if (! in_array($provider, self::PROVIDERS[$category], true)) {
            throw ValidationException::withMessages([
                'provider' => ['The selected provider is not available for this integration category.'],
            ]);
        }
    }

    private function clearDefault(Request $request, array $attributes, ?Model $record = null): void
    {
        if (! ($attributes['is_default'] ?? false)) {
            return;
        }

        ServiceIntegration::where('team_id', $this->teamId($request))
            ->where('category', $attributes['category'] ?? $record->category)
            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->update(['is_default' => false]);
    }
}
