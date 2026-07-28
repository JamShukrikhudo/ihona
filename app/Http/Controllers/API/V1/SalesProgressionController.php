<?php

namespace App\Http\Controllers\API\V1;

use App\Models\SalesProgression;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesProgressionController extends TenantCrudController
{
    protected string $model = SalesProgression::class;
    protected string $routeParameter = 'sales_progression';
    protected array $searchable = ['buyer_solicitor_name', 'seller_solicitor_name', 'notes'];
    protected array $filterable = ['property_id', 'agent_id', 'stage'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);
        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'transaction_id' => ['nullable', Rule::exists('transactions', 'id')->where('team_id', $teamId)],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'stage' => ['sometimes', Rule::in(array_keys(SalesProgression::STAGES))],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'offer_accepted_date' => ['nullable', 'date'],
            'exchange_date' => ['nullable', 'date'],
            'completion_date' => ['nullable', 'date'],
            'buyer_solicitor_name' => ['nullable', 'string', 'max:255'],
            'buyer_solicitor_email' => ['nullable', 'email'],
            'buyer_solicitor_phone' => ['nullable', 'string', 'max:50'],
            'seller_solicitor_name' => ['nullable', 'string', 'max:255'],
            'seller_solicitor_email' => ['nullable', 'email'],
            'seller_solicitor_phone' => ['nullable', 'string', 'max:50'],
            'mortgage_lender' => ['nullable', 'string', 'max:255'],
            'mortgage_broker' => ['nullable', 'string', 'max:255'],
            'checklist_items' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
