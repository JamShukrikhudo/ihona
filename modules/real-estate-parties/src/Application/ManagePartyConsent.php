<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Parties\Models\Party;

final class ManagePartyConsent
{
    public function handle(Party $party, int|string $teamId, bool $granted): Party
    {
        if ((string) $party->team_id !== (string) $teamId) {
            throw ValidationException::withMessages(['party' => 'The party does not belong to this team.']);
        }

        return DB::transaction(function () use ($party, $granted): Party {
            $metadata = $party->metadata ?? [];
            $metadata['consent'] = ['granted' => $granted, 'changed_at' => now()->toISOString()];
            $party->forceFill(['consent_at' => $granted ? now() : null, 'metadata' => $metadata])->save();

            return $party->refresh();
        });
    }
}
