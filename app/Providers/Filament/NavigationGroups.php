<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\AccountSetupWizard;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Panel;
use Liberu\Foundation\ApplicationFilament\Pages\Overview;
use Liberu\Foundation\IdentityFilament\Resources\UserResource;
use Liberu\Foundation\ModuleManagerFilament\Pages\FoundationOperations;
use Liberu\Foundation\OrganizationsFilament\Resources\TeamResource;
use Liberu\Foundation\SessionsDevicesFilament\Pages\AccountSecurity;
use Liberu\Foundation\SettingsFilament\Pages\ManageSiteSettings;
use Liberu\RealEstate\CoreFilament\Resources\AgencyResource;
use Liberu\RealEstate\CoreFilament\Resources\BranchResource;
use Liberu\RealEstate\CoreFilament\Resources\StatusDefinitionResource;
use Liberu\RealEstate\CoreFilament\Resources\TerritoryResource;
use Liberu\RealEstate\InstructionsFilament\Resources\InstructionResource;
use Liberu\RealEstate\LettingsFilament\Resources\LettingResource;
use Liberu\RealEstate\LettingsFilament\Resources\RentalApplicationResource;
use Liberu\RealEstate\ListingsFilament\Resources\ListingResource;
use Liberu\RealEstate\MarketingFilament\Resources\MarketingCampaignResource;
use Liberu\RealEstate\MarketingFilament\Resources\NewsArticleResource;
use Liberu\RealEstate\MatchingFilament\Resources\MatchProfileResource;
use Liberu\RealEstate\MediaAndDocumentsFilament\Resources\MediaDocumentResource;
use Liberu\RealEstate\OffersFilament\Resources\OfferResource;
use Liberu\RealEstate\OnTheMarketFilament\Resources\OnTheMarketSyncResource;
use Liberu\RealEstate\PartiesFilament\Resources\PartyResource;
use Liberu\RealEstate\PortalsReportingFilament\Resources\PortalReportResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyCategoryResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertySavedSearchResource;
use Liberu\RealEstate\PropertiesFilament\Resources\PropertyTemplateResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\InspectionResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\MaintenanceRequestResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\ManagementRecordResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource;
use Liberu\RealEstate\PropertyManagementFilament\Resources\WorkOrderResource;
use Liberu\RealEstate\RightmoveFilament\Resources\RightmoveSyncResource;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;
use Liberu\RealEstate\ValuationsFilament\Resources\ValuationResource;
use Liberu\RealEstate\ViewingsFilament\Resources\ViewingResource;
use Liberu\RealEstate\ZooplaFilament\Resources\ZooplaSyncResource;

final class NavigationGroups
{
    /**
     * Apply the host application's information architecture after all module
     * plugins have registered their pages and resources.
     */
    public static function configure(Panel $panel): void
    {
        foreach (self::groupsFor($panel->getId()) as $group => $classes) {
            foreach ($classes as $sort => $class) {
                if (class_exists($class) && method_exists($class, 'navigationGroup')) {
                    $class::navigationGroup($group);

                    if (method_exists($class, 'navigationSort')) {
                        $class::navigationSort($sort);
                    }
                }
            }
        }
    }

    /**
     * @return array<string, array<int, class-string>>
     */
    private static function groupsFor(string $panel): array
    {
        if ($panel === 'app') {
            return [
                'Account & support' => [
                    10 => AccountSetupWizard::class,
                    20 => AccountSecurity::class,
                ],
            ];
        }

        return [
            'Sales & lettings' => [
                10 => PropertyResource::class,
                20 => ListingResource::class,
                30 => ViewingResource::class,
                40 => OfferResource::class,
                50 => LettingResource::class,
                60 => RentalApplicationResource::class,
                70 => SalesProgressionResource::class,
            ],
            'People & relationships' => [
                10 => PartyResource::class,
            ],
            'Property management' => [
                10 => InspectionResource::class,
                20 => MaintenanceRequestResource::class,
                30 => ManagementRecordResource::class,
                40 => VendorQuoteResource::class,
                50 => WorkOrderResource::class,
            ],
            'Marketing & portals' => [
                10 => MarketingCampaignResource::class,
                20 => NewsArticleResource::class,
                30 => PortalReportResource::class,
                40 => RightmoveSyncResource::class,
                50 => ZooplaSyncResource::class,
                60 => OnTheMarketSyncResource::class,
            ],
            'Insights & tools' => [
                10 => MatchProfileResource::class,
                20 => ValuationResource::class,
                30 => PropertySavedSearchResource::class,
            ],
            'Instructions & media' => [
                10 => InstructionResource::class,
                20 => MediaDocumentResource::class,
            ],
            'Organisation' => [
                10 => TeamResource::class,
                20 => UserResource::class,
                30 => RoleResource::class,
            ],
            'Property configuration' => [
                10 => AgencyResource::class,
                20 => BranchResource::class,
                30 => TerritoryResource::class,
                40 => StatusDefinitionResource::class,
                50 => PropertyCategoryResource::class,
                60 => PropertyTemplateResource::class,
            ],
            'Platform settings' => [
                10 => ManageSiteSettings::class,
                20 => Overview::class,
                30 => \Liberu\Foundation\LocalizationCoreFilament\Pages\Overview::class,
                40 => \Liberu\Foundation\CurrencyContextFilament\Pages\Overview::class,
                50 => FoundationOperations::class,
            ],
            'Integrations & API' => [
                10 => \Liberu\Foundation\IntegrationsFilament\Pages\Overview::class,
                20 => \Liberu\Foundation\ApiAccessFilament\Pages\Overview::class,
                30 => \Liberu\Foundation\WebhooksFilament\Pages\Overview::class,
                40 => \Liberu\Foundation\AnalyticsCoreFilament\Pages\Overview::class,
                50 => \Liberu\Foundation\AnalyticsGoogleFilament\Pages\Overview::class,
                60 => \Liberu\Foundation\AnalyticsMetaFilament\Pages\Overview::class,
            ],
            'Operations & diagnostics' => [
                10 => \Liberu\Foundation\SchedulerQueuesFilament\Pages\Overview::class,
                20 => \Liberu\Foundation\ObservabilityFilament\Pages\Overview::class,
                30 => \Liberu\Foundation\NotificationsFilament\Pages\Overview::class,
                40 => \Liberu\Foundation\FilesMediaFilament\Pages\Overview::class,
                50 => \Liberu\Foundation\ImportExportFilament\Pages\Overview::class,
                60 => \Liberu\Foundation\DeveloperExperienceFilament\Pages\Overview::class,
                70 => \Liberu\Foundation\AuditFilament\Pages\Overview::class,
            ],
        ];
    }
}
