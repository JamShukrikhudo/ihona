<?php

declare(strict_types=1);

use Liberu\RealEstate\Core\Domain\CoreCapabilityDefinition;
use Liberu\RealEstate\Instructions\Domain\InstructionsCapabilityDefinition;
use Liberu\RealEstate\Lettings\Domain\LettingCapabilityDefinition;
use Liberu\RealEstate\Listings\Domain\ListingsCapabilityDefinition;
use Liberu\RealEstate\Marketing\Domain\MarketingCapabilityDefinition;
use Liberu\RealEstate\Matching\Domain\MatchingCapabilityDefinition;
use Liberu\RealEstate\MediaAndDocuments\Domain\MediaAndDocumentsCapabilityDefinition;
use Liberu\RealEstate\Offers\Domain\OffersCapabilityDefinition;
use Liberu\RealEstate\Parties\Domain\PartiesCapabilityDefinition;
use Liberu\RealEstate\PortalsReporting\Domain\PortalsReportingCapabilityDefinition;
use Liberu\RealEstate\Properties\Domain\PropertiesCapabilityDefinition;
use Liberu\RealEstate\PropertyManagement\Domain\ManagementCapabilityDefinition;
use Liberu\RealEstate\SalesProgression\Domain\SalesProgressionCapabilityDefinition;
use Liberu\RealEstate\Valuations\Domain\ValuationsCapabilityDefinition;
use Liberu\RealEstate\Viewings\Domain\ViewingsCapabilityDefinition;

it('keeps every open real-estate issue feature represented by its domain boundary', function (): void {
    $root = dirname(__DIR__, 2);
    $definitions = [
        'real-estate-core' => CoreCapabilityDefinition::class,
        'real-estate-parties' => PartiesCapabilityDefinition::class,
        'real-estate-properties' => PropertiesCapabilityDefinition::class,
        'real-estate-instructions' => InstructionsCapabilityDefinition::class,
        'real-estate-listings' => ListingsCapabilityDefinition::class,
        'real-estate-lettings' => LettingCapabilityDefinition::class,
        'real-estate-property-management' => ManagementCapabilityDefinition::class,
        'real-estate-matching' => MatchingCapabilityDefinition::class,
        'real-estate-viewings' => ViewingsCapabilityDefinition::class,
        'real-estate-offers' => OffersCapabilityDefinition::class,
        'real-estate-sales-progression' => SalesProgressionCapabilityDefinition::class,
        'real-estate-marketing' => MarketingCapabilityDefinition::class,
        'real-estate-portals-reporting' => PortalsReportingCapabilityDefinition::class,
        'real-estate-valuations' => ValuationsCapabilityDefinition::class,
        'real-estate-media-and-documents' => MediaAndDocumentsCapabilityDefinition::class,
    ];

    foreach ($definitions as $module => $definition) {
        $manifest = json_decode(file_get_contents("{$root}/modules/{$module}/module.json"), true, flags: JSON_THROW_ON_ERROR);
        $features = array_values(array_map(
            static fn (array $capability): string => $capability['label'],
            $definition::all(),
        ));

        expect($features)->toBe($manifest['features'], "{$module} has drifted from its issue feature scope.");
    }
});

it('keeps Packagist names free of the source repository module prefix', function (): void {
    $root = dirname(__DIR__, 2);

    foreach (glob("{$root}/modules/real-estate-*/composer.json") ?: [] as $composerFile) {
        $package = json_decode(file_get_contents($composerFile), true, flags: JSON_THROW_ON_ERROR);

        expect($package['name'])->not->toContain('/module-');
    }
});
