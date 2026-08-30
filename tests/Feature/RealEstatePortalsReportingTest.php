<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\PortalsReporting\Application\CreatePortalReport;
use Liberu\RealEstate\PortalsReporting\Application\DeletePortalReport;
use Liberu\RealEstate\PortalsReporting\Application\RecordPortalMetric;
use Liberu\RealEstate\PortalsReporting\Domain\PortalMetric;
use Liberu\RealEstate\PortalsReporting\Models\PortalReport;

uses(RefreshDatabase::class);

it('creates a team portal report with payload and metrics', function (): void {
    $report = app(CreatePortalReport::class)->handle(31, 7, [
        'portal' => 'rightmove',
        'report_type' => 'listing_delivery',
        'listing_id' => 12,
        'payload' => ['external_id' => 'abc-123'],
        'metrics' => ['views' => 42],
    ]);

    expect($report)
        ->toBeInstanceOf(PortalReport::class)
        ->team_id->toBe(31)
        ->status->value->toBe('draft')
        ->metrics->toMatchArray(['views' => 42]);
});

it('requires portal identity and archives only inside its team', function (): void {
    expect(fn () => app(CreatePortalReport::class)->handle(31, 7, []))
        ->toThrow(ValidationException::class);

    $report = app(CreatePortalReport::class)->handle(31, 7, ['portal' => 'zoopla', 'report_type' => 'delivery']);

    expect(fn () => app(DeletePortalReport::class)->handle($report, 32))
        ->toThrow(ValidationException::class);

    app(DeletePortalReport::class)->handle($report, 31);

    expect($report->fresh()->trashed())->toBeTrue();
});

it('records typed reporting metrics without replacing existing metrics', function (): void {
    $report = app(CreatePortalReport::class)->handle(31, 7, ['portal' => 'rightmove', 'report_type' => 'pipeline', 'metrics' => ['views' => 42]]);

    app(RecordPortalMetric::class)->handle($report, 31, PortalMetric::Conversion, 18.5);

    expect($report->refresh()->metrics)->toMatchArray(['views' => 42, 'conversion' => 18.5]);
});
