<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

final class DeleteSalesProgression
{
    public function handle(SalesProgression $progression, int|string $teamId): void
    {
        if ((string) $progression->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['progression' => 'The progression does not belong to this team.']);
        }$progression->delete();
    }
}
