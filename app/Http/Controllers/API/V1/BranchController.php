<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BranchController extends TenantCrudController
{
    protected string $model = Branch::class;
    protected string $routeParameter = 'branch';
    protected array $searchable = ['name', 'address', 'email'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
