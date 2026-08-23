<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\DeleteMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

uses(RefreshDatabase::class);

it('creates and updates a team media document', function (): void {
    $document = app(CreateMediaDocument::class)->handle(1, 5, [
        'kind' => 'PHOTO',
        'path' => 'properties/1/front.jpg',
        'title' => 'Front elevation',
    ]);

    expect($document->kind)->toBe('photo')->and($document->team_id)->toBe(1);

    $updated = app(UpdateMediaDocument::class)->handle($document, 1, ['sort_order' => 2]);
    expect($updated->sort_order)->toBe(2);
});

it('rejects empty paths and soft deletes within the team', function (): void {
    expect(fn () => app(CreateMediaDocument::class)->handle(1, 5, ['kind' => 'document', 'path' => '']))
        ->toThrow(ValidationException::class);

    $document = MediaDocument::query()->create(['team_id' => 1, 'kind' => 'document', 'path' => 'contracts/1.pdf']);
    app(DeleteMediaDocument::class)->handle($document, 1);

    expect(MediaDocument::withTrashed()->find($document->id)->deleted_at)->not->toBeNull();
});
