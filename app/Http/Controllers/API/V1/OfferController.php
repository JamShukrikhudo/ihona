<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfferController extends TenantCrudController
{
    protected string $model = Offer::class;

    protected string $routeParameter = 'offer';

    protected array $filterable = ['property_id', 'contact_id', 'status', 'negotiator_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('properties', 'id')->where('team_id', $teamId),
            ],
            'contact_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('contacts', 'id')->where('team_id', $teamId),
            ],
            'negotiator_id' => [
                'nullable',
                Rule::exists('team_user', 'user_id')->where('team_id', $teamId),
            ],
            'amount' => [$record ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'status' => ['sometimes', Rule::in(['pending', 'accepted', 'rejected', 'withdrawn', 'superseded'])],
            'mortgage_status' => ['nullable', Rule::in(['cash', 'agreement_in_principle', 'approved', 'required', 'unknown'])],
            'chain_information' => ['nullable', 'string'],
            'conditions' => ['nullable', 'string'],
            'offered_at' => [$record ? 'sometimes' : 'required', 'date'],
            'responded_at' => ['nullable', 'date'],
        ];
    }
}
