<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\AgencyRole;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Property;
use App\Services\DocumentAccessService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DocumentController extends TenantCrudController
{
    private const RELATED_TYPES = [
        'contact' => Contact::class,
        'company' => Company::class,
        'property' => Property::class,
    ];

    protected string $model = Document::class;

    protected string $routeParameter = 'document';

    protected array $searchable = ['title', 'description'];

    protected array $filterable = ['property_id', 'file_type', 'is_signable', 'visibility', 'documentable_type'];

    public function __construct(private readonly DocumentAccessService $access) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_path' => ['nullable', 'string', 'max:2048'],
            'file_type' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'integer', 'min:0'],
            'property_id' => ['nullable', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'related_type' => ['nullable', 'required_with:related_id', Rule::in(array_keys(self::RELATED_TYPES))],
            'related_id' => ['nullable', 'required_with:related_type', 'integer'],
            'category_ids' => ['nullable', 'array', 'max:50'],
            'category_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('document_categories', 'id')->where('team_id', $teamId),
            ],
            'visibility' => ['sometimes', Rule::in(['team', 'private', 'restricted'])],
            'allowed_user_ids' => ['nullable', 'array', 'max:100'],
            'allowed_user_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('team_user', 'user_id')->where('team_id', $teamId),
            ],
            'allowed_roles' => ['nullable', 'array', 'max:10'],
            'allowed_roles.*' => ['string', 'distinct', Rule::in(AgencyRole::assignable())],
            'is_signable' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;

        return $this->prepareDocument($request, $attributes);
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        return $this->prepareDocument($request, $attributes, $record);
    }

    protected function afterCreate(Request $request, Model $record): void
    {
        $record->categories()->sync($request->input('category_ids', []));
    }

    protected function afterUpdate(Request $request, Model $record): void
    {
        if ($request->has('category_ids')) {
            $record->categories()->sync($request->input('category_ids', []));
        }
    }

    protected function teamQuery(Request $request): Builder
    {
        return $this->access->query($request->user(), $this->teamId($request));
    }

    private function prepareDocument(Request $request, array $attributes, ?Model $record = null): array
    {
        unset($attributes['category_ids']);

        if (array_key_exists('related_type', $attributes) || array_key_exists('related_id', $attributes)) {
            if (blank($attributes['related_type'] ?? null) && blank($attributes['related_id'] ?? null)) {
                $attributes['documentable_type'] = null;
                $attributes['documentable_id'] = null;
            } else {
                $model = self::RELATED_TYPES[$attributes['related_type']];
                $related = $model::where('team_id', $this->teamId($request))
                    ->findOrFail($attributes['related_id']);
                $attributes['documentable_type'] = $related->getMorphClass();
                $attributes['documentable_id'] = $related->getKey();
                if ($related instanceof Property) {
                    $attributes['property_id'] = $related->id;
                }
            }
            unset($attributes['related_type'], $attributes['related_id']);
        }

        $visibility = $attributes['visibility'] ?? $record?->visibility ?? 'team';
        $allowedUsers = $attributes['allowed_user_ids'] ?? $record?->allowed_user_ids ?? [];
        $allowedRoles = $attributes['allowed_roles'] ?? $record?->allowed_roles ?? [];
        if ($visibility === 'restricted' && $allowedUsers === [] && $allowedRoles === []) {
            throw ValidationException::withMessages([
                'visibility' => ['Restricted documents require at least one allowed user or role.'],
            ]);
        }
        if ($visibility !== 'restricted') {
            $attributes['allowed_user_ids'] = null;
            $attributes['allowed_roles'] = null;
        }

        return $attributes;
    }
}
