<?php

namespace App\Http\Controllers\API\V1;

use App\Models\AgencyTask;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends TenantCrudController
{
    protected string $model = AgencyTask::class;

    protected string $routeParameter = 'task';

    protected array $searchable = ['title', 'description'];

    protected array $filterable = ['status', 'priority', 'assigned_to', 'branch_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $teamId)],
            'assigned_to' => [
                'nullable',
                Rule::exists('team_user', 'user_id')->where('team_id', $teamId),
            ],
            'priority' => ['sometimes', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['sometimes', Rule::in(['open', 'in_progress', 'completed', 'cancelled'])],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'checklist' => ['nullable', 'array'],
            'checklist.*.label' => ['required_with:checklist', 'string', 'max:255'],
            'checklist.*.completed' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $attributes;
    }
}
