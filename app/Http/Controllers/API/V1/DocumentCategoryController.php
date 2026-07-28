<?php

namespace App\Http\Controllers\API\V1;

use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentCategoryController extends TenantCrudController
{
    protected string $model = DocumentCategory::class;

    protected string $routeParameter = 'document_category';

    protected array $searchable = ['name', 'description'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'name' => [
                $record ? 'sometimes' : 'required',
                'string',
                'max:100',
                Rule::unique('document_categories', 'name')
                    ->where('team_id', $this->teamId($request))
                    ->ignore($record?->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;

        return $attributes;
    }
}
