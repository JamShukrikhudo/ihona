<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Liberu\RealEstate\Core\Models\AuditEntry;

final class RecordAuditEntry
{
    /** @param array<string, mixed> $metadata */
    public function handle(int|string $teamId, int|string|null $actorId, string $event, ?string $subjectType = null, int|string|null $subjectId = null, array $metadata = []): AuditEntry
    {
        return AuditEntry::query()->create(['team_id' => $teamId, 'actor_id' => $actorId, 'event' => $event, 'subject_type' => $subjectType, 'subject_id' => $subjectId, 'metadata' => $metadata]);
    }
}
