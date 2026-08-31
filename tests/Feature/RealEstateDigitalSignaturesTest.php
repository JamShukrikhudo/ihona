<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Application\SignMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\VerifyDigitalSignature;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;

uses(RefreshDatabase::class);

it('records and verifies an integrity-protected document signature', function (): void {
    $document = MediaDocument::query()->create(['team_id' => 1, 'kind' => 'document', 'path' => 'contracts/lease.pdf', 'is_signable' => true]);
    $signature = app(SignMediaDocument::class)->handle($document, 1, 5, 'signed-by-party', '127.0.0.1', 'test-agent');

    expect($signature->signature_hash)->toBe(hash('sha256', 'signed-by-party'))->and(app(VerifyDigitalSignature::class)->handle($signature, 1))->toBeTrue()->and($signature->fresh()->isVerified())->toBeTrue();
});

it('rejects unsigned documents and altered signature data', function (): void {
    $document = MediaDocument::query()->create(['team_id' => 1, 'kind' => 'document', 'path' => 'contracts/lease.pdf']);
    expect(fn () => app(SignMediaDocument::class)->handle($document, 1, 5, 'signature'))->toThrow(ValidationException::class);

    $document->update(['is_signable' => true]);
    $signature = app(SignMediaDocument::class)->handle($document, 1, 5, 'signature');
    $signature->forceFill(['signature_data' => 'altered'])->save();
    expect(fn () => app(VerifyDigitalSignature::class)->handle($signature, 1))->toThrow(ValidationException::class);
});
