<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends TenantCrudController
{
    protected string $model = Department::class;

    protected string $routeParameter = 'department';

    protected array $searchable = ['name', 'description'];

    protected array $filterable = ['manager_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('departments')->where('team_id', $teamId)->ignore($record),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
            'manager_id' => [
                'nullable',
                Rule::exists('team_user', 'user_id')->where('team_id', $teamId),
            ],
        ];
    }
}
