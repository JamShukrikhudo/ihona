<?php

namespace App\Http\Controllers\API\V1;

use App\Models\AutomationRule;
use App\Models\AutomationRun;
use App\Services\AutomationEngine;
use App\Services\NotificationDispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AutomationController extends TenantCrudController
{
    protected string $model = AutomationRule::class;

    protected string $routeParameter = 'automation';

    protected array $searchable = ['name', 'trigger'];

    protected array $filterable = ['trigger', 'active'];

    public function __construct(private readonly AutomationEngine $engine) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'trigger' => [$record ? 'sometimes' : 'required', 'string', 'max:100'],
            'active' => ['sometimes', 'boolean'],
            'conditions' => ['nullable', 'array'],
            'conditions.*.field' => ['required_with:conditions', 'string', 'max:255'],
            'conditions.*.operator' => ['required_with:conditions', Rule::in(['equals', 'not_equals', 'contains', 'greater_than', 'less_than'])],
            'conditions.*.value' => ['present'],
            'actions' => [$record ? 'sometimes' : 'required', 'array', 'min:1'],
            'actions.*.type' => ['required', Rule::in(['create_task', 'notify_user', 'update_property_status'])],
            'actions.*.title' => ['required_if:actions.*.type,create_task,notify_user', 'string', 'max:255'],
            'actions.*.description' => ['nullable', 'string'],
            'actions.*.body' => ['nullable', 'string'],
            'actions.*.channels' => ['nullable', 'array', 'min:1'],
            'actions.*.channels.*' => ['required', Rule::in(NotificationDispatcher::CHANNELS)],
            'actions.*.priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'actions.*.due_in_days' => ['nullable', 'integer', 'between:0,3650'],
            'actions.*.assigned_to' => ['nullable', 'integer'],
            'actions.*.user_id' => ['required_if:actions.*.type,notify_user', 'integer'],
            'actions.*.property_id' => ['nullable', 'integer'],
            'actions.*.status' => ['required_if:actions.*.type,update_property_status', 'string', 'max:100'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $attributes;
    }

    public function run(Request $request, int $automation): JsonResponse
    {
        $rule = $this->teamQuery($request)->findOrFail($automation);
        $validated = $request->validate(['context' => ['nullable', 'array']]);
        $run = $this->engine->run($rule, $validated['context'] ?? [], $request->user());

        return response()->json(['data' => $run], $run->status === 'failed' ? 422 : 200);
    }

    public function runs(Request $request): JsonResponse
    {
        $query = AutomationRun::where('team_id', $this->teamId($request))
            ->with('automationRule')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('automation_id'), fn ($query) => $query->where('automation_rule_id', $request->integer('automation_id')));

        return response()->json($query->latest('started_at')->paginate(
            min(max($request->integer('per_page', 20), 1), 100)
        ));
    }
}
