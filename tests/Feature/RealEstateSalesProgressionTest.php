<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\SalesProgression\Application\CreateSalesProgression;
use Liberu\RealEstate\SalesProgression\Application\DeleteSalesProgression;
use Liberu\RealEstate\SalesProgression\Models\SalesProgression;

uses(RefreshDatabase::class);

it('creates a team-scoped progression with milestones and chain metadata', function (): void {
    $progression = app(CreateSalesProgression::class)->handle(31, 7, [
        'subject' => '12 Example Street sale',
        'property_id' => 12,
        'offer_id' => 8,
        'milestones' => ['memorandum_issued' => true],
        'chain' => ['upstream' => ['subject' => '14 Example Street']],
    ]);

    expect($progression)
        ->toBeInstanceOf(SalesProgression::class)
        ->team_id->toBe(31)
        ->status->value->toBe('in_progress')
        ->milestones->toMatchArray(['memorandum_issued' => true]);
});

it('requires a subject and archives only a progression in its team', function (): void {
    expect(fn (): SalesProgression => app(CreateSalesProgression::class)->handle(31, 7, []))
        ->toThrow(ValidationException::class);

    $progression = app(CreateSalesProgression::class)->handle(31, 7, ['subject' => 'Sale']);

    expect(fn () => app(DeleteSalesProgression::class)->handle($progression, 32))
        ->toThrow(ValidationException::class);

    app(DeleteSalesProgression::class)->handle($progression, 31);

    expect($progression->fresh()->trashed())->toBeTrue();
});
