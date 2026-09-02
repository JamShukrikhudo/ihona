<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\PropertyManagement\Application\CreateVendorQuote;
use Liberu\RealEstate\PropertyManagement\Application\DecideVendorQuote;
use Liberu\RealEstate\PropertyManagement\Models\VendorQuote;
use Liberu\RealEstate\PropertyManagementApi\Http\Resources\VendorQuoteResource;

final class VendorQuoteController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return VendorQuoteResource::collection(VendorQuote::query()->forTeam($team)->latest()->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateVendorQuote $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new VendorQuoteResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, VendorQuote $contractorQuote): JsonResponse
    {
        $this->assertTeam($request, $contractorQuote);

        return (new VendorQuoteResource($contractorQuote))->response();
    }

    public function update(Request $request, VendorQuote $contractorQuote): JsonResponse
    {
        $this->assertTeam($request, $contractorQuote);
        $contractorQuote->forceFill($request->validate($this->rules(true)))->save();

        return (new VendorQuoteResource($contractorQuote->refresh()))->response();
    }

    public function decide(Request $request, VendorQuote $contractorQuote, DecideVendorQuote $decide): JsonResponse
    {
        $this->assertTeam($request, $contractorQuote);
        $data = $request->validate(['decision' => ['required', Rule::in(['accepted', 'rejected'])], 'rejection_reason' => ['nullable', 'string', 'max:2000']]);

        return (new VendorQuoteResource($decide->handle($contractorQuote, $request->user()->current_team_id, $request->user()->getAuthIdentifier(), $data['decision'], $data['rejection_reason'] ?? null)))->response();
    }

    private function assertTeam(Request $request, VendorQuote $quote): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $quote->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return ['vendor_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'maintenance_request_id' => [...$presence, 'nullable', 'integer'], 'work_description' => [...$presence, ...($sometimes ? ['string'] : ['required', 'string'])], 'quote_amount' => [...$presence, ...($sometimes ? ['numeric', 'min:0'] : ['required', 'numeric', 'min:0'])], 'labor_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'materials_cost' => [...$presence, 'nullable', 'numeric', 'min:0'], 'additional_costs' => [...$presence, 'nullable', 'numeric', 'min:0'], 'quote_date' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'valid_until' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'estimated_duration' => [...$presence, 'nullable', 'integer', 'min:0'], 'start_date' => [...$presence, 'nullable', 'date'], 'completion_date' => [...$presence, 'nullable', 'date'], 'terms_conditions' => [...$presence, 'nullable', 'string'], 'status' => [...$presence, Rule::in(['pending', 'accepted', 'rejected', 'expired', 'withdrawn'])], 'notes' => [...$presence, 'nullable', 'string'], 'rejection_reason' => [...$presence, 'nullable', 'string', 'max:2000']];
    }
}
