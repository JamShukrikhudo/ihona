<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Lettings\Application\CreateRentalCharge;
use Liberu\RealEstate\Lettings\Application\UpdateRentalCharge;
use Liberu\RealEstate\Lettings\Models\RentalCharge;
use Liberu\RealEstate\LettingsApi\Http\Resources\RentalChargeResource;

final class RentalChargeController
{
    public function index(Request $request): JsonResponse
    {
        $team = $request->user()?->current_team_id;
        abort_unless($team !== null, 403);

        return RentalChargeResource::collection(RentalCharge::query()->forTeam($team)->latest('charge_date')->paginate(min(100, max(1, $request->integer('page_size', 25)))))->response();
    }

    public function store(Request $request, CreateRentalCharge $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);

        return (new RentalChargeResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $request->validate($this->rules(false)))))->response()->setStatusCode(201);
    }

    public function show(Request $request, RentalCharge $rentalCharge): JsonResponse
    {
        $this->assertTeam($request, $rentalCharge);

        return (new RentalChargeResource($rentalCharge))->response();
    }

    public function update(Request $request, RentalCharge $rentalCharge, UpdateRentalCharge $update): JsonResponse
    {
        $this->assertTeam($request, $rentalCharge);

        return (new RentalChargeResource($update->handle($rentalCharge, $request->user()->current_team_id, $request->validate($this->rules(true)))))->response();
    }

    public function destroy(Request $request, RentalCharge $rentalCharge): JsonResponse
    {
        $this->assertTeam($request, $rentalCharge);
        $rentalCharge->delete();

        return response()->json(['deleted' => true]);
    }

    private function assertTeam(Request $request, RentalCharge $charge): void
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $charge->team_id, 404);
    }

    private function rules(bool $sometimes): array
    {
        $presence = $sometimes ? ['sometimes'] : [];

        return ['property_id' => [...$presence, ...($sometimes ? ['integer'] : ['required', 'integer'])], 'tenant_party_id' => [...$presence, 'nullable', 'integer'], 'lease_agreement_id' => [...$presence, 'nullable', 'integer'], 'amount' => [...$presence, ...($sometimes ? ['numeric', 'min:0'] : ['required', 'numeric', 'min:0'])], 'charge_date' => [...$presence, ...($sometimes ? ['date'] : ['required', 'date'])], 'description' => [...$presence, ...($sometimes ? ['string', 'max:255'] : ['required', 'string', 'max:255'])], 'status' => [...$presence, Rule::in(['pending', 'paid', 'overdue'])]];
    }
}
