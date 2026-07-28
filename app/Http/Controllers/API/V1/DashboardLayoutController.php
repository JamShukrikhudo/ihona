<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DashboardLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardLayoutController extends TenantCrudController
{
    protected string $model = DashboardLayout::class;

    protected string $routeParameter = 'dashboard_layout';

    protected array $searchable = ['name'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'widgets' => [$record ? 'sometimes' : 'required', 'array', 'max:30'],
            'widgets.*.type' => ['required', Rule::in([
                'kpis', 'calendar', 'tasks', 'recent_activity', 'sales', 'lettings',
                'new_leads', 'property_pipeline', 'maintenance', 'branch_statistics',
            ])],
            'widgets.*.position' => ['nullable', 'array'],
            'widgets.*.settings' => ['nullable', 'array'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    protected function teamQuery(Request $request): Builder
    {
        return parent::teamQuery($request)
            ->where(fn (Builder $query) => $query
                ->whereNull('user_id')
                ->orWhere('user_id', $request->user()->id));
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;
        $this->clearDefault($request, $attributes);

        return $attributes;
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        $this->clearDefault($request, $attributes, $record->id);

        return $attributes;
    }

    private function clearDefault(Request $request, array $attributes, ?int $except = null): void
    {
        if (($attributes['is_default'] ?? false) !== true) {
            return;
        }

        DashboardLayout::query()
            ->where('team_id', $this->teamId($request))
            ->where('user_id', $request->user()->id)
            ->when($except, fn (Builder $query) => $query->whereKeyNot($except))
            ->update(['is_default' => false]);
    }
}
