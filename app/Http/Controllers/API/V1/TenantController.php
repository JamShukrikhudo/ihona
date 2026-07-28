<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TenantController extends TenantCrudController
{
    protected string $model = Tenant::class;
    protected string $routeParameter = 'tenant';
    protected array $searchable = ['name', 'first_name', 'last_name', 'email', 'phone'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                $record ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('tenants', 'email')->ignore($record?->getKey()),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
        ];
    }
}
