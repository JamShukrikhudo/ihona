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

it('requires authenticated, throttled API boundaries and OpenAPI 3.1 contracts', function (): void {
    $root = dirname(__DIR__, 2);

    foreach (glob("{$root}/modules/real-estate-*-api/routes/api.php") ?: [] as $routeFile) {
        $routes = file_get_contents($routeFile);

        expect($routes)->toContain("'auth:sanctum'")
            ->and($routes)->toContain("'throttle:api'");
    }

    foreach (glob("{$root}/modules/real-estate-*-api/openapi/v1/*.yaml") ?: [] as $openApiFile) {
        $openApi = file_get_contents($openApiFile);

        expect($openApi)->toContain('openapi: 3.1.0')
            ->and($openApi)->toContain('securitySchemes:')
            ->and($openApi)->toContain('operationId:')
            ->and($openApi)->toContain('security:')
            ->and($openApi)->toContain('schemas:')
            ->and($openApi)->toContain('Error:')
            ->and($openApi)->toContain('PaginationMeta:')
            ->and($openApi)->toContain('Idempotency-Key');
    }
});

it('keeps API controllers behind explicit response resources', function (): void {
    $root = dirname(__DIR__, 2);

    foreach (glob("{$root}/modules/real-estate-*-api/src/Http/Controllers/*Controller.php") ?: [] as $controllerFile) {
        $controller = file_get_contents($controllerFile);

        expect($controller)->toContain('Http\\Resources\\');
    }
});

it('keeps Livewire list surfaces validated and stateful', function (): void {
    $root = dirname(__DIR__, 2);

    foreach (glob("{$root}/modules/real-estate-*-livewire/src/Components/*.php") ?: [] as $componentFile) {
        expect(file_get_contents($componentFile))->toContain("#[Validate('nullable|string|max:255')]");
    }

    foreach (glob("{$root}/modules/real-estate-*-livewire/resources/views/*.blade.php") ?: [] as $viewFile) {
        $view = file_get_contents($viewFile);

        expect($view)->toContain('wire:loading')->and($view)->toContain('@empty');
    }
});

it('keeps advanced Livewire search ordering bounded', function (): void {
    $source = file_get_contents(base_path('modules/real-estate-properties-livewire/src/Components/AdvancedPropertySearch.php'));
    $view = file_get_contents(base_path('modules/real-estate-properties-livewire/resources/views/advanced-property-search.blade.php'));

    expect($source)->toContain('public string $sortBy = \'created_at\';')
        ->toContain("'sortBy' => ['required', 'string', 'in:created_at,updated_at,price,year_built,bedrooms,bathrooms,area_sqft,address']")
        ->toContain('->sorted($this->sortBy, $this->sortDirection)')
        ->and($view)->toContain('advanced-sort-by')
        ->toContain('advanced-sort-direction');
});

it('preserves Livewire property filters in the URL', function (): void {
    $propertyList = file_get_contents(base_path('modules/real-estate-properties-livewire/src/Components/PropertyList.php'));
    $advancedSearch = file_get_contents(base_path('modules/real-estate-properties-livewire/src/Components/AdvancedPropertySearch.php'));

    expect($propertyList)->toContain('protected $queryString = [')
        ->toContain("'selectedAmenities' => ['except' => []]")
        ->toContain("'sortDirection' => ['except' => 'desc']")
        ->and($advancedSearch)->toContain('protected $queryString = [')
        ->toContain("'sortBy' => ['except' => 'created_at']")
        ->toContain("'featuredOnly' => ['except' => false]");
});

it('keeps the API property response aligned with legacy listing data', function (): void {
    $resource = file_get_contents(base_path('modules/real-estate-properties-api/src/Http/Resources/PropertyResource.php'));
    $openApi = file_get_contents(base_path('modules/real-estate-properties-api/openapi/v1/real-estate-properties.yaml'));

    foreach (['title', 'description', 'currency', 'reception_rooms', 'year_built', 'postal_code', 'energy_score', 'list_date', 'is_featured', 'ar_tour_enabled', 'insurance_expiry_date', 'rightmove_id', 'zoopla_id', 'onthemarket_id'] as $field) {
        expect($resource)->toContain("'{$field}'");
        expect($openApi)->toContain($field);
    }

    expect($openApi)->toContain("#/components/schemas/PropertyPage")
        ->toContain("#/components/schemas/PropertyResponse");
});

it('does not narrow advanced Livewire search when no template is selected', function (): void {
    $source = file_get_contents(base_path('modules/real-estate-properties-livewire/src/Components/AdvancedPropertySearch.php'));

    expect($source)->toContain('->when($this->propertyTemplateId !== null, fn ($query) => $query->where(\'property_template_id\', $this->propertyTemplateId))');
});

it('keeps property template filters available across API and Filament', function (): void {
    $api = file_get_contents(base_path('modules/real-estate-properties-api/src/Http/Controllers/PropertyController.php'));
    $openApi = file_get_contents(base_path('modules/real-estate-properties-api/openapi/v1/real-estate-properties.yaml'));
    $filament = file_get_contents(base_path('modules/real-estate-properties-filament/src/Resources/PropertyResource.php'));

    expect($api)->toContain("'property_template_id' => ['sometimes', 'nullable'")
        ->toContain('where(\'property_template_id\', $filters[\'property_template_id\'])')
        ->and($openApi)->toContain('name: property_template_id')
        ->and($filament)->toContain("SelectFilter::make('property_template_id')")
        ->toContain('PropertyTemplate::query()->forTeam');
});

it('keeps Filament resources tenant-scoped and lifecycle-delegated', function (): void {
    $root = dirname(__DIR__, 2);

    foreach (glob("{$root}/modules/real-estate-*-filament/src/Resources/*Resource.php") ?: [] as $resourceFile) {
        $resource = file_get_contents($resourceFile);

        expect($resource)->toContain('getEloquentQuery')
            ->and($resource)->toContain('current_team_id');
    }

    foreach (glob("{$root}/modules/real-estate-*-filament/src/Resources/*Resource/Pages/Create*.php") ?: [] as $pageFile) {
        expect(file_get_contents($pageFile))->toContain('handleRecordCreation');
    }

    foreach (glob("{$root}/modules/real-estate-*-filament/src/Resources/*Resource/Pages/Edit*.php") ?: [] as $pageFile) {
        expect(file_get_contents($pageFile))->toContain('handleRecordUpdate');
    }
});
