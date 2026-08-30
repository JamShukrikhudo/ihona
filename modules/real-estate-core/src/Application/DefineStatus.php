<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Core\Application;

use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Core\Models\StatusDefinition;

final class DefineStatus
{
    public function handle(int|string $teamId, string $entity, string $key, string $label, bool $active = true): StatusDefinition
    {
        if (trim($entity) === '' || trim($key) === '' || trim($label) === '') {
            throw ValidationException::withMessages(['status' => 'Entity, key, and label are required.']);
        }

        return StatusDefinition::query()->updateOrCreate(['team_id' => $teamId, 'entity' => trim($entity), 'key' => trim($key)], ['label' => trim($label), 'active' => $active]);
    }
}
