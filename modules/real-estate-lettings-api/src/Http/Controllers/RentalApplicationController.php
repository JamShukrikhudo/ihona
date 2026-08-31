<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Lettings\Application\CreateRentalApplication;
use Liberu\RealEstate\Lettings\Application\DecideRentalApplication;
use Liberu\RealEstate\Lettings\Application\UpdateRentalApplicationScreening;
use Liberu\RealEstate\Lettings\Models\RentalApplication;
use Liberu\RealEstate\LettingsApi\Http\Resources\RentalApplicationResource;

final class RentalApplicationController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return RentalApplicationResource::collection(RentalApplication::query()->forTeam($team)->latest()->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateRentalApplication $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new RentalApplicationResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, RentalApplication $rentalApplication): JsonResponse
    {
        $this->assertTeam($request, $rentalApplication);

        return (new RentalApplicationResource($rentalApplication))->response();
    }

    public function update(Request $request, RentalApplication $rentalApplication, UpdateRentalApplicationScreening $update): JsonResponse
    {
        $this->assertTeam($request, $rentalApplication);

        return (new RentalApplicationResource($update->handle($rentalApplication, $request->user()->current_team_id, $request->validate($this->rules(true)))))->response();
    }

    public function screening(Request $request, RentalApplication $rentalApplication, UpdateRentalApplicationScreening $update): JsonResponse
    {
        $this->assertTeam($request, $rentalApplication);
        $application = $update->handle($rentalApplication, $request->user()->current_team_id, $request->validate($this->screeningRules()));

        return response()->json(['data' => new RentalApplicationResource($application), 'screening_complete' => $application->isScreeningComplete(), 'screening_passed' => $application->isScreeningPassed()]);
    }

    public function decide(Request $request, RentalApplication $rentalApplication, DecideRentalApplication $decide): JsonResponse
    {
        $this->assertTeam($request, $rentalApplication);
        $data = $request->validate(['decision' => ['required', Rule::in(['approved', 'rejected'])], 'notes' => ['nullable', 'string', 'max:2000']]);

        return (new RentalApplicationResource($decide->handle($rentalApplication, $request->user()->current_team_id, $request->user()->getAuthIdentifier(), $data['decision'], $data['notes'] ?? null)))->response();
    }

    private function assertTeam(Request $request, RentalApplication $application): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $application->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return [
            'property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'party_id' => [...$presence, 'nullable', 'integer'], 'applicant_user_id' => [...$presence, 'nullable', 'integer'],
            'status' => [...$presence, Rule::in(['draft', 'submitted'])], 'employment_status' => [...$presence, 'nullable', 'string', 'max:50'], 'annual_income' => [...$presence, 'nullable', 'numeric', 'min:0'], 'monthly_income' => [...$presence, 'nullable', 'numeric', 'min:0'], 'application_date' => [...$presence, 'nullable', 'date'], 'desired_move_in_date' => [...$presence, 'nullable', 'date'], 'lease_end_date' => [...$presence, 'nullable', 'date'], 'ethereum_address' => [...$presence, 'nullable', 'string', 'max:255'], 'guarantors' => [...$presence, 'nullable', 'array'], 'screening_consent_at' => [...$presence, 'nullable', 'date'],
        ];
    }

    private function screeningRules(): array
    {
        return ['background_check_status' => ['sometimes', Rule::in(['pending', 'passed', 'failed', 'not_required'])], 'credit_report_status' => ['sometimes', Rule::in(['pending', 'excellent', 'good', 'fair', 'poor', 'failed', 'not_required'])], 'rental_history_status' => ['sometimes', Rule::in(['pending', 'good', 'satisfactory', 'unsatisfactory', 'not_available'])], 'affordability_status' => ['sometimes', Rule::in(['pending', 'passed', 'failed', 'manual_review', 'not_required'])], 'right_to_rent_status' => ['sometimes', Rule::in(['pending', 'verified', 'failed', 'not_required'])], 'employer_reference' => ['sometimes', 'nullable', 'array'], 'landlord_reference' => ['sometimes', 'nullable', 'array']];
    }
}
