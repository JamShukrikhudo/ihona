<?php

namespace App\Http\Controllers\API\V1;

use App\Models\SavedReport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SavedReportController extends TenantCrudController
{
    protected string $model = SavedReport::class;

    protected string $routeParameter = 'saved_report';

    protected array $searchable = ['name'];

    protected array $filterable = ['type', 'chart_type', 'is_shared'];

    protected function teamQuery(Request $request): Builder
    {
        return parent::teamQuery($request)
            ->where(fn (Builder $query) => $query
                ->where('is_shared', true)
                ->orWhere('created_by', $request->user()->id));
    }

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'type' => [$record ? 'sometimes' : 'required', Rule::in(['dashboard', 'pipeline'])],
            'filters' => ['nullable', 'array'],
            'filters.from' => ['nullable', 'date'],
            'filters.to' => ['nullable', 'date', 'after_or_equal:filters.from'],
            'filters.branch_id' => ['nullable', 'integer'],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['string', 'max:100'],
            'chart_type' => ['nullable', Rule::in(['bar', 'line', 'pie', 'doughnut', 'table'])],
            'is_shared' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $attributes;
    }
}
