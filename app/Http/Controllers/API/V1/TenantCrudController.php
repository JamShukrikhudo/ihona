<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class TenantCrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    protected string $routeParameter;

    protected array $searchable = [];

    protected array $filterable = [];

    abstract protected function rules(Request $request, ?Model $record = null): array;

    public function index(Request $request): JsonResponse
    {
        $query = $this->teamQuery($request);
        $this->applyFilters($query, $request);

        $perPage = min(max($request->integer('per_page', 20), 1), 100);

        return response()->json(
            $query->latest($query->getModel()->getQualifiedKeyName())->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $attributes = $request->validate($this->rules($request));
        $attributes['team_id'] = $this->teamId($request);
        $attributes = $this->prepareForCreate($request, $attributes);

        $record = $this->model::create($attributes);

        return response()->json(['data' => $record->fresh()], 201);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->record($request)]);
    }

    public function update(Request $request): JsonResponse
    {
        $record = $this->record($request);
        $attributes = $request->validate($this->rules($request, $record));
        $record->update($this->prepareForUpdate($request, $record, $attributes));

        return response()->json(['data' => $record->fresh()]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->record($request)->delete();

        return response()->json(null, 204);
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        return $attributes;
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        return $attributes;
    }

    protected function teamQuery(Request $request): Builder
    {
        return $this->model::query()->where('team_id', $this->teamId($request));
    }

    protected function record(Request $request): Model
    {
        return $this->teamQuery($request)
            ->findOrFail($request->route($this->routeParameter));
    }

    protected function teamId(Request $request): int
    {
        $user = $request->user();
        $teamId = $user?->current_team_id;

        if (! $teamId || ! $user->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to before using this endpoint.'],
            ]);
        }

        return (int) $teamId;
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        foreach ($this->filterable as $field) {
            if ($request->filled("filter.$field")) {
                $query->where($field, $request->input("filter.$field"));
            }
        }

        if ($request->filled('search') && $this->searchable !== []) {
            $term = '%'.$request->string('search')->trim().'%';
            $query->where(function (Builder $query) use ($term) {
                foreach ($this->searchable as $index => $field) {
                    $index === 0
                        ? $query->where($field, 'like', $term)
                        : $query->orWhere($field, 'like', $term);
                }
            });
        }
    }
}
