<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends TenantCrudController
{
    protected string $model = Company::class;

    protected string $routeParameter = 'company';

    protected array $searchable = ['name', 'registration_number', 'email'];

    protected array $filterable = ['type', 'branch_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in([
                'developer', 'property_company', 'housing_association',
                'investment_firm', 'contractor', 'other',
            ])],
            'branch_id' => [
                'nullable',
                Rule::exists('branches', 'id')->where('team_id', $this->teamId($request)),
            ],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'address' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
