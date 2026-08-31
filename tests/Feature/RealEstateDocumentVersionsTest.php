<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateDocumentVersion;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

uses(RefreshDatabase::class);

it('creates sequential document versions with checksums and links them to the document', function (): void {
    $document = MediaDocument::query()->create(['team_id' => 1, 'kind' => 'document', 'path' => 'contracts/lease.pdf']);
    $create = app(CreateDocumentVersion::class);
    $first = $create->handle($document, 1, 5, ['file_name' => 'lease-v1.pdf', 'file_path' => 'contracts/lease-v1.pdf']);
    $second = $create->handle($document, 1, 5, ['file_name' => 'lease-v2.pdf', 'file_path' => 'contracts/lease-v2.pdf', 'notes' => 'Updated clauses']);

    expect($first->version)->toBe(1)->and($first->checksum)->toBe(hash('sha256', 'contracts/lease-v1.pdf'))->and($second->version)->toBe(2)->and($document->fresh()->versions)->toHaveCount(2)->and($document->fresh()->versions->first()->version)->toBe(2);
});
