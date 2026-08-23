<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Valuations\Domain\ValuationStatus;
use Liberu\RealEstate\Valuations\Models\Valuation;

final class CreateValuation
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Valuation
    {
        $subject = trim((string) ($attributes['subject'] ?? ''));
        if ($subject === '') {
            throw ValidationException::withMessages(['subject' => 'A valuation subject is required.']);
        }

        return DB::transaction(fn (): Valuation => Valuation::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'party_id' => $attributes['party_id'] ?? null, 'subject' => $subject, 'status' => ValuationStatus::Draft, 'valued_amount' => $attributes['valued_amount'] ?? null, 'fee_amount' => $attributes['fee_amount'] ?? null, 'comparable_data' => $attributes['comparable_data'] ?? [], 'recommendation' => $attributes['recommendation'] ?? [], 'scheduled_at' => $attributes['scheduled_at'] ?? null]));
    }
}
