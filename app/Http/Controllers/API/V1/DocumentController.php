<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentController extends TenantCrudController
{
    protected string $model = Document::class;
    protected string $routeParameter = 'document';
    protected array $searchable = ['title', 'description'];
    protected array $filterable = ['property_id', 'file_type', 'is_signable'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:2048'],
            'file_type' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'integer', 'min:0'],
            'property_id' => ['nullable', Rule::exists('properties', 'id')->where('team_id', $this->teamId($request))],
            'is_signable' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;
        return $attributes;
    }
}
