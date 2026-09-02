<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Lettings\Domain\RentalApplicationStatus;
use Liberu\RealEstate\Lettings\Models\RentalApplication;

final class CreateRentalApplication
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): RentalApplication
    {
        if (! isset($attributes['property_id'])) {
            throw ValidationException::withMessages(['property_id' => 'A property is required.']);
        }

        $status = RentalApplicationStatus::tryFrom((string) ($attributes['status'] ?? RentalApplicationStatus::Submitted->value));
        if ($status === null || ! in_array($status, [RentalApplicationStatus::Draft, RentalApplicationStatus::Submitted], true)) {
            throw ValidationException::withMessages(['status' => 'Applications can only be created as drafts or submitted applications.']);
        }

        $submittedAt = $status === RentalApplicationStatus::Submitted ? now() : null;

        return DB::transaction(fn (): RentalApplication => RentalApplication::query()->create([
            ...$attributes,
            'team_id' => $teamId,
            'applicant_user_id' => $attributes['applicant_user_id'] ?? $actorId,
            'status' => $status,
            'application_date' => $attributes['application_date'] ?? now()->toDateString(),
            'submitted_at' => $attributes['submitted_at'] ?? $submittedAt,
        ]));
    }
}
