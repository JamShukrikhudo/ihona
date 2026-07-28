<?php

namespace App\Http\Controllers\API\V1;

use App\Models\PortalIntegration;
use App\Models\PortalListing;
use App\Models\PortalSyncRun;
use App\Models\Property;
use App\Services\PortalSyncService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PortalIntegrationController extends TenantCrudController
{
    protected string $model = PortalIntegration::class;
    protected string $routeParameter = 'portal_integration';
    protected array $searchable = ['provider', 'country'];
    protected array $filterable = ['provider', 'country', 'branch_id', 'channel', 'active'];

    public function __construct(private readonly PortalSyncService $syncService)
    {
    }

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'provider' => [$record ? 'sometimes' : 'required', 'string', 'max:100'],
            'country' => [$record ? 'sometimes' : 'required', 'string', 'size:2'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $this->teamId($request))],
            'channel' => ['sometimes', Rule::in(['sales', 'lettings', 'both'])],
            'sync_frequency' => ['sometimes', Rule::in(['manual', 'hourly', 'daily', 'weekly'])],
            'credentials' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function publish(Request $request, int $portalIntegration, int $property): JsonResponse
    {
        $integration = $this->teamQuery($request)->findOrFail($portalIntegration);
        $record = Property::where('team_id', $this->teamId($request))->findOrFail($property);
        $listing = PortalListing::updateOrCreate(
            ['portal_integration_id' => $integration->id, 'property_id' => $record->id],
            ['team_id' => $this->teamId($request), 'status' => 'pending', 'last_error' => null],
        );
        return response()->json(['data' => $listing->fresh()], $listing->wasRecentlyCreated ? 201 : 200);
    }

    public function unpublish(Request $request, int $portalIntegration, int $property): JsonResponse
    {
        $integration = $this->teamQuery($request)->findOrFail($portalIntegration);
        $listing = $integration->listings()
            ->where('team_id', $this->teamId($request))
            ->where('property_id', $property)
            ->firstOrFail();
        $listing->update(['status' => 'withdrawn']);
        return response()->json(['data' => $listing->fresh()]);
    }

    public function sync(Request $request, int $portalIntegration): JsonResponse
    {
        $integration = $this->teamQuery($request)->findOrFail($portalIntegration);
        abort_unless($integration->active, 422, 'Inactive portal integrations cannot be synchronized.');
        return response()->json(['data' => $this->syncService->sync($integration, $request->user())]);
    }

    public function runs(Request $request): JsonResponse
    {
        $query = PortalSyncRun::where('team_id', $this->teamId($request))
            ->when($request->filled('portal_integration_id'), fn ($query) => $query->where('portal_integration_id', $request->integer('portal_integration_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')));
        return response()->json($query->latest('started_at')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }
}
