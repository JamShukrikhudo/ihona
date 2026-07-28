<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Contact;
use App\Services\WorkflowNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends TenantCrudController
{
    protected string $model = Contact::class;

    protected string $routeParameter = 'contact';

    protected array $searchable = ['first_name', 'last_name', 'notes'];

    protected array $filterable = ['type', 'status', 'branch_id', 'company_id'];

    public function __construct(private readonly WorkflowNotifier $notifications) {}

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'type' => ['sometimes', 'required', Rule::in([
                'applicant', 'vendor', 'landlord', 'tenant', 'buyer', 'solicitor',
                'developer', 'contractor', 'investor', 'business',
            ])],
            'title' => ['nullable', 'string', 'max:30'],
            'first_name' => [$record ? 'sometimes' : 'required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $teamId)],
            'company_id' => ['nullable', Rule::exists('companies', 'id')->where('team_id', $teamId)],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['email:rfc', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*' => ['string', 'max:50'],
            'addresses' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'preferred_language' => ['nullable', 'string', 'max:10'],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'archived'])],
            'last_contacted_at' => ['nullable', 'date'],
        ];
    }

    protected function afterCreate(Request $request, Model $record): void
    {
        $this->notifications->notify(
            $this->teamId($request),
            $request->user(),
            'enquiry.created',
            'New enquiry',
            trim("{$record->first_name} {$record->last_name}")." was added as {$record->type}.",
            ['contact_id' => $record->getKey(), 'contact_type' => $record->type],
        );
    }
}
