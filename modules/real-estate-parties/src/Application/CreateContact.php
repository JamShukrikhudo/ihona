<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Application;

use Illuminate\Support\Facades\DB;
use Liberu\RealEstate\Parties\Models\Contact;

final class CreateContact
{
    public function handle(int|string $teamId, int|string $actorId, array $attributes): Contact
    {
        return DB::transaction(fn (): Contact => Contact::query()->create([...$attributes, 'team_id' => $teamId]));
    }
}
