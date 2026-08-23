<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionStatus;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

final class CreateSalesProgression
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): SalesProgression
    {
        $subject = trim((string) ($attributes['subject'] ?? ''));
        if ($subject === '') {
            throw ValidationException::withMessages(['subject' => 'A progression subject is required.']);
        }

        return DB::transaction(fn (): SalesProgression => SalesProgression::query()->create(['team_id' => $teamId, 'created_by' => $actorId, 'property_id' => $attributes['property_id'] ?? null, 'offer_id' => $attributes['offer_id'] ?? null, 'subject' => $subject, 'status' => $attributes['status'] ?? SalesProgressionStatus::InProgress, 'milestones' => $attributes['milestones'] ?? [], 'chain' => $attributes['chain'] ?? [], 'professionals' => $attributes['professionals'] ?? [], 'completion_controls' => $attributes['completion_controls'] ?? [], 'exchanged_at' => $attributes['exchanged_at'] ?? null, 'completed_at' => $attributes['completed_at'] ?? null, 'notes' => $attributes['notes'] ?? null]));
    }
}
