<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Lettings\Application\CreateLeaseAgreement;
use Liberu\RealEstate\Lettings\Application\RenewLeaseAgreement;
use Liberu\RealEstate\Lettings\Application\ServeLeaseNotice;
use Liberu\RealEstate\Lettings\Models\LeaseAgreement;
use Liberu\RealEstate\LettingsApi\Http\Resources\LeaseAgreementResource;

final class LeaseAgreementController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return LeaseAgreementResource::collection(LeaseAgreement::query()->forTeam($team)->latest()->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateLeaseAgreement $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new LeaseAgreementResource($create->handle($user->current_team_id, $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, LeaseAgreement $tenancyAgreement): JsonResponse
    {
        $this->assertTeam($request, $tenancyAgreement);

        return (new LeaseAgreementResource($tenancyAgreement))->response();
    }

    public function update(Request $request, LeaseAgreement $tenancyAgreement): JsonResponse
    {
        $this->assertTeam($request, $tenancyAgreement);
        $tenancyAgreement->forceFill($request->validate($this->rules(true)))->save();

        return (new LeaseAgreementResource($tenancyAgreement->refresh()))->response();
    }

    public function renew(Request $request, LeaseAgreement $tenancyAgreement, RenewLeaseAgreement $renew): JsonResponse
    {
        $this->assertTeam($request, $tenancyAgreement);

        return (new LeaseAgreementResource($renew->handle($tenancyAgreement, $request->user()->current_team_id, $request->validate(['start_date' => ['required', 'date'], 'end_date' => ['required', 'date'], 'monthly_rent' => ['required', 'numeric', 'min:0'], 'security_deposit' => ['nullable', 'numeric', 'min:0']]))))->response()->setStatusCode(201);
    }

    public function notice(Request $request, LeaseAgreement $tenancyAgreement, ServeLeaseNotice $notice): JsonResponse
    {
        $this->assertTeam($request, $tenancyAgreement);

        return (new LeaseAgreementResource($notice->handle($tenancyAgreement, $request->user()->current_team_id, $request->validate(['notice_type' => ['required', Rule::in(['landlord', 'tenant', 'mutual'])], 'notice_served_at' => ['required', 'date'], 'notice_expires_at' => ['required', 'date'], 'end_reason' => ['nullable', 'string', 'max:1000']]))))->response();
    }

    private function assertTeam(Request $request, LeaseAgreement $agreement): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $agreement->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return [
            'property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'tenant_party_id' => [...$presence, 'nullable', 'integer'], 'landlord_party_id' => [...$presence, 'nullable', 'integer'],
            'start_date' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'end_date' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'monthly_rent' => [...$presence, ...($sometimes ? ['numeric', 'min:0'] : ['required', 'numeric', 'min:0'])], 'security_deposit' => [...$presence, 'nullable', 'numeric', 'min:0'],
            'status' => [...$presence, Rule::in(['draft', 'pending_signature', 'active', 'notice_served', 'ended', 'terminated', 'renewed'])], 'payment_frequency' => [...$presence, 'nullable', Rule::in(['weekly', 'fortnightly', 'monthly', 'quarterly', 'annually'])], 'deposit_scheme' => [...$presence, 'nullable', 'string', 'max:255'], 'deposit_reference' => [...$presence, 'nullable', 'string', 'max:255'], 'terms' => [...$presence, 'nullable', 'string'], 'content' => [...$presence, 'nullable', 'string'], 'terms_and_conditions' => [...$presence, 'nullable', 'string'], 'is_signed' => [...$presence, 'boolean'], 'landlord_signed' => [...$presence, 'boolean'], 'tenant_signed' => [...$presence, 'boolean'], 'notice_type' => [...$presence, 'nullable', Rule::in(['landlord', 'tenant', 'mutual'])], 'notice_served_at' => [...$presence, 'nullable', 'date'], 'notice_expires_at' => [...$presence, 'nullable', 'date'], 'ended_at' => [...$presence, 'nullable', 'date'], 'end_reason' => [...$presence, 'nullable', 'string', 'max:1000'],
        ];
    }
}
