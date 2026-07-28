<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Communication;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunicationController extends TenantCrudController
{
    private const RELATED_TYPES = [
        'contact' => Contact::class,
        'company' => Company::class,
        'property' => Property::class,
    ];

    protected string $model = Communication::class;

    protected string $routeParameter = 'communication';

    protected array $searchable = ['subject', 'body', 'from_address', 'to_address'];

    protected array $filterable = ['channel', 'direction', 'status'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        return [
            'related_type' => ['nullable', 'required_with:related_id', Rule::in(array_keys(self::RELATED_TYPES))],
            'related_id' => ['nullable', 'required_with:related_type', 'integer'],
            'channel' => [$record ? 'sometimes' : 'required', Rule::in(['email', 'sms', 'phone', 'note', 'letter'])],
            'direction' => ['sometimes', Rule::in(['inbound', 'outbound', 'internal'])],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'from_address' => ['nullable', 'string', 'max:255'],
            'to_address' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in(['draft', 'queued', 'sent', 'delivered', 'failed', 'recorded'])],
            'metadata' => ['nullable', 'array'],
            'occurred_at' => [$record ? 'sometimes' : 'required', 'date'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;

        return $this->resolveRelatedRecord($request, $attributes);
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        return $this->resolveRelatedRecord($request, $attributes);
    }

    private function resolveRelatedRecord(Request $request, array $attributes): array
    {
        if (! isset($attributes['related_type'], $attributes['related_id'])) {
            unset($attributes['related_type'], $attributes['related_id']);

            return $attributes;
        }

        $model = self::RELATED_TYPES[$attributes['related_type']];
        $related = $model::query()
            ->where('team_id', $this->teamId($request))
            ->findOrFail($attributes['related_id']);

        unset($attributes['related_type'], $attributes['related_id']);
        $attributes['communicable_type'] = $related->getMorphClass();
        $attributes['communicable_id'] = $related->getKey();

        return $attributes;
    }
}
