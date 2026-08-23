<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

final class UpdateSalesProgression
{
    public function handle(SalesProgression $progression, int|string $teamId, array $attributes): SalesProgression
    {
        if ((string) $progression->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['progression' => 'The progression does not belong to this team.']);
        }

        $data = $attributes;
        if (array_key_exists('subject', $data) && trim((string) $data['subject']) === '') {
            throw ValidationException::withMessages(['subject' => 'A progression subject is required.']);
        }

        $progression->fill($data)->save();

        return $progression->refresh();
    }
}
