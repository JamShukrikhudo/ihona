<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\DeleteMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\GeneratePropertyBrochure;
use Liberu\RealEstate\MediaAndDocuments\Application\ReorderMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\SetMediaDocumentRetention;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaRights;
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

it('manages rights, ordering, and retention through dedicated actions', function (): void {
    $document = app(CreateMediaDocument::class)->handle(1, 5, ['kind' => 'photo', 'path' => 'properties/1/front.jpg']);

    app(UpdateMediaRights::class)->handle($document, 1, ['license' => 'perpetual', 'attribution_required' => true]);
    app(ReorderMediaDocument::class)->handle($document, 1, 4);
    app(SetMediaDocumentRetention::class)->handle($document, 1, '2030-01-01');

    $document->refresh();
    expect($document->rights['license'])->toBe('perpetual')
        ->and($document->sort_order)->toBe(4)
        ->and($document->retention_until->toDateString())->toBe('2030-01-01');
});

it('generates escaped brochure data and HTML from trusted property fields', function (): void {
    $brochure = app(GeneratePropertyBrochure::class)->handle(['id' => 7, 'title' => '<Home>', 'price' => 300000, 'features' => ['Garden']]);

    expect($brochure['data']['property']['formatted_price'])->toBe('£300,000')
        ->and($brochure['html'])->toContain('&lt;Home&gt;')
        ->and($brochure['html'])->not->toContain('<h1><Home>');
});

it('supports site-plan gallery media and only accepts explicit public URLs', function (): void {
    $document = app(CreateMediaDocument::class)->handle(1, 5, [
        'kind' => 'SITEPLAN',
        'path' => 'properties/1/site-plan.png',
        'metadata' => ['public_url' => 'https://cdn.example.test/site-plan.png'],
    ]);

    expect($document->galleryKind())->toBe('site plan')
        ->and($document->publicUrl())->toBe('https://cdn.example.test/site-plan.png');

    $document->update(['metadata' => ['public_url' => 'not-a-url']]);

    expect($document->publicUrl())->toBeNull();
});

it('identifies video media separately from gallery media', function (): void {
    $video = app(CreateMediaDocument::class)->handle(1, 5, [
        'kind' => 'video',
        'path' => 'properties/1/tour.mp4',
        'metadata' => ['public_url' => 'https://cdn.example.test/tour.mp4'],
    ]);

    expect($video->isVideo())->toBeTrue()
        ->and($video->publicUrl())->toBe('https://cdn.example.test/tour.mp4');
});
