<?php

namespace App\Http\Controllers\API\V1;

use App\Models\RentalApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RentalApplicationController extends TenantCrudController
{
    protected string $model = RentalApplication::class;

    protected string $routeParameter = 'rental_application';

    protected array $searchable = ['decision_notes'];

    protected array $filterable = [
        'property_id', 'applicant_id', 'status', 'affordability_status',
        'right_to_rent_status', 'background_check_status',
    ];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('properties', 'id')->where('team_id', $teamId),
            ],
            'applicant_id' => [
                $record ? 'sometimes' : 'required',
                Rule::exists('contacts', 'id')
                    ->where('team_id', $teamId)
                    ->whereIn('type', ['applicant', 'tenant']),
            ],
            'status' => [$record ? 'prohibited' : 'sometimes', Rule::in(['draft', 'submitted'])],
            'employment_status' => ['nullable', Rule::in([
                'employed', 'self_employed', 'contractor', 'student',
                'retired', 'unemployed', 'other',
            ])],
            'annual_income' => ['nullable', 'numeric', 'min:0'],
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
            'application_date' => ['nullable', 'date'],
            'desired_move_in_date' => ['nullable', 'date'],
            'guarantors' => ['nullable', 'array'],
            'guarantors.*.name' => ['required_with:guarantors', 'string', 'max:255'],
            'guarantors.*.email' => ['required_with:guarantors', 'email', 'max:255'],
            'guarantors.*.phone' => ['nullable', 'string', 'max:50'],
            'guarantors.*.relationship' => ['nullable', 'string', 'max:100'],
            'guarantors.*.annual_income' => ['nullable', 'numeric', 'min:0'],
            'guarantors.*.consent_received' => ['sometimes', 'boolean'],
            'screening_consent_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['status'] ??= 'submitted';
        $attributes['application_date'] ??= now()->toDateString();
        $attributes['submitted_at'] = $attributes['status'] === 'submitted' ? now() : null;

        return $attributes;
    }

    public function screening(Request $request, int $rentalApplication): JsonResponse
    {
        /** @var RentalApplication $application */
        $application = $this->teamQuery($request)->findOrFail($rentalApplication);
        $attributes = $request->validate([
            'background_check_status' => ['sometimes', Rule::in(['pending', 'passed', 'failed', 'not_required'])],
            'credit_report_status' => ['sometimes', Rule::in(['pending', 'excellent', 'good', 'fair', 'poor', 'failed', 'not_required'])],
            'rental_history_status' => ['sometimes', Rule::in(['pending', 'good', 'satisfactory', 'unsatisfactory', 'not_available'])],
            'affordability_status' => ['sometimes', Rule::in(['pending', 'passed', 'failed', 'manual_review'])],
            'right_to_rent_status' => ['sometimes', Rule::in(['pending', 'verified', 'failed', 'not_required'])],
            'employer_reference' => ['sometimes', 'nullable', 'array'],
            'employer_reference.status' => ['required_with:employer_reference', Rule::in(['pending', 'verified', 'failed', 'not_required'])],
            'employer_reference.referee_name' => ['nullable', 'string', 'max:255'],
            'employer_reference.referee_email' => ['nullable', 'email', 'max:255'],
            'employer_reference.notes' => ['nullable', 'string', 'max:2000'],
            'landlord_reference' => ['sometimes', 'nullable', 'array'],
            'landlord_reference.status' => ['required_with:landlord_reference', Rule::in(['pending', 'verified', 'failed', 'not_required'])],
            'landlord_reference.referee_name' => ['nullable', 'string', 'max:255'],
            'landlord_reference.referee_email' => ['nullable', 'email', 'max:255'],
            'landlord_reference.notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($application->status === 'approved' || $application->status === 'rejected') {
            throw ValidationException::withMessages([
                'status' => ['Screening cannot be changed after a final decision.'],
            ]);
        }

        $application->update($attributes);

        return response()->json([
            'data' => $application->fresh(),
            'screening_complete' => $application->isScreeningComplete(),
            'screening_passed' => $application->isScreeningPassed(),
        ]);
    }

    public function decide(Request $request, int $rentalApplication): JsonResponse
    {
        /** @var RentalApplication $application */
        $application = $this->teamQuery($request)->findOrFail($rentalApplication);
        $attributes = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'rejected'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! in_array($application->status, ['submitted', 'under_review'], true)) {
            throw ValidationException::withMessages([
                'decision' => ['Only submitted applications can receive a final decision.'],
            ]);
        }

        $application->update([
            'status' => $attributes['decision'],
            'decided_at' => now(),
            'decided_by' => $request->user()->id,
            'decision_notes' => $attributes['notes'] ?? null,
        ]);

        return response()->json(['data' => $application->fresh()]);
    }
}
