<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\RealEstate\MediaAndDocuments\Application\CanAccessMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

uses(RefreshDatabase::class);

it('enforces team, private, and restricted media document access', function (): void {
    $access = app(CanAccessMediaDocument::class);
    $team = MediaDocument::query()->create(['team_id' => 1, 'created_by' => 5, 'kind' => 'document', 'path' => 'team.pdf']);
    $private = MediaDocument::query()->create(['team_id' => 1, 'created_by' => 5, 'kind' => 'document', 'path' => 'private.pdf', 'visibility' => 'private']);
    $restricted = MediaDocument::query()->create(['team_id' => 1, 'created_by' => 5, 'kind' => 'document', 'path' => 'restricted.pdf', 'visibility' => 'restricted', 'allowed_user_ids' => [7], 'allowed_roles' => ['manager']]);

    expect($access->handle($team, 99))->toBeTrue()->and($access->handle($private, 99))->toBeFalse()->and($access->handle($private, 5))->toBeTrue()->and($access->handle($restricted, 7))->toBeTrue()->and($access->handle($restricted, 99, ['manager']))->toBeTrue()->and($access->handle($restricted, 99, [], true))->toBeTrue();
});
