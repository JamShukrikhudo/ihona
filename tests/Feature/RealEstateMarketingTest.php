<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Marketing\Application\CreateMarketingCampaign;
use Liberu\RealEstate\Marketing\Application\DeleteMarketingCampaign;
use Liberu\RealEstate\Marketing\Application\UpdateMarketingCampaignSection;
use Liberu\RealEstate\Marketing\Domain\MarketingCampaignSection;
use Liberu\RealEstate\Marketing\Models\MarketingCampaign;

uses(RefreshDatabase::class);

it('creates a team campaign with audience and delivery metadata', function (): void {
    $campaign = app(CreateMarketingCampaign::class)->handle(31, 7, [
        'name' => 'Spring launch',
        'channel' => 'email',
        'audience' => ['segments' => ['buyers']],
        'content' => ['subject' => 'New listing'],
        'metrics' => ['planned_recipients' => 50],
    ]);

    expect($campaign)
        ->toBeInstanceOf(MarketingCampaign::class)
        ->team_id->toBe(31)
        ->status->value->toBe('draft')
        ->audience->toMatchArray(['segments' => ['buyers']]);
});

it('requires campaign identity and archives only within its team', function (): void {
    expect(fn () => app(CreateMarketingCampaign::class)->handle(31, 7, []))
        ->toThrow(ValidationException::class);

    $campaign = app(CreateMarketingCampaign::class)->handle(31, 7, ['name' => 'Campaign', 'channel' => 'social']);

    expect(fn () => app(DeleteMarketingCampaign::class)->handle($campaign, 32))
        ->toThrow(ValidationException::class);

    app(DeleteMarketingCampaign::class)->handle($campaign, 31);

    expect($campaign->fresh()->trashed())->toBeTrue();
});

it('updates campaign content sections independently from lifecycle state', function (): void {
    $campaign = app(CreateMarketingCampaign::class)->handle(31, 7, ['name' => 'Campaign', 'channel' => 'email']);

    app(UpdateMarketingCampaignSection::class)->handle($campaign, 31, MarketingCampaignSection::Content, ['subject' => 'New homes']);

    expect($campaign->refresh()->content)->toBe(['subject' => 'New homes']);
});
