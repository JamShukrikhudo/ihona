<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\AccountSetupWizard;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
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
            foreach ($classes as $class) {
                if (class_exists($class) && method_exists($class, 'navigationGroup')) {
                    $class::navigationGroup($group);
                }
            }
        }
    }

    /**
     * @return array<string, list<class-string>>
     */
    private static function groupsFor(string $panel): array
    {
        if ($panel === 'app') {
            return [
                'Account' => [
                    AccountSetupWizard::class,
                    AccountSecurity::class,
                ],
            ];
        }

        return [
            'Real Estate' => [
                PropertyResource::class,
                ListingResource::class,
                ViewingResource::class,
                OfferResource::class,
                LettingResource::class,
                RentalApplicationResource::class,
                SalesProgressionResource::class,
            ],
            'People & Relationships' => [
                PartyResource::class,
            ],
            'Property Management' => [
                InspectionResource::class,
                MaintenanceRequestResource::class,
                ManagementRecordResource::class,
                VendorQuoteResource::class,
                WorkOrderResource::class,
            ],
            'Marketing & Portals' => [
                MarketingCampaignResource::class,
                NewsArticleResource::class,
                PortalReportResource::class,
                RightmoveSyncResource::class,
                ZooplaSyncResource::class,
                OnTheMarketSyncResource::class,
            ],
            'Insights & Tools' => [
                MatchProfileResource::class,
                ValuationResource::class,
                PropertySavedSearchResource::class,
            ],
            'Content' => [
                MediaDocumentResource::class,
                InstructionResource::class,
            ],
            'Organisation' => [
                TeamResource::class,
                UserResource::class,
                RoleResource::class,
            ],
            'Configuration' => [
                AgencyResource::class,
                BranchResource::class,
                TerritoryResource::class,
                StatusDefinitionResource::class,
                PropertyCategoryResource::class,
                PropertyTemplateResource::class,
            ],
            'Platform' => [
                ManageSiteSettings::class,
                Overview::class,
                \Liberu\Foundation\LocalizationCoreFilament\Pages\Overview::class,
                \Liberu\Foundation\CurrencyContextFilament\Pages\Overview::class,
                FoundationOperations::class,
            ],
            'Integrations' => [
                \Liberu\Foundation\IntegrationsFilament\Pages\Overview::class,
                \Liberu\Foundation\ApiAccessFilament\Pages\Overview::class,
                \Liberu\Foundation\WebhooksFilament\Pages\Overview::class,
                \Liberu\Foundation\AnalyticsCoreFilament\Pages\Overview::class,
                \Liberu\Foundation\AnalyticsGoogleFilament\Pages\Overview::class,
                \Liberu\Foundation\AnalyticsMetaFilament\Pages\Overview::class,
            ],
            'Operations' => [
                \Liberu\Foundation\SchedulerQueuesFilament\Pages\Overview::class,
                \Liberu\Foundation\ObservabilityFilament\Pages\Overview::class,
                \Liberu\Foundation\NotificationsFilament\Pages\Overview::class,
                \Liberu\Foundation\FilesMediaFilament\Pages\Overview::class,
                \Liberu\Foundation\ImportExportFilament\Pages\Overview::class,
                \Liberu\Foundation\DeveloperExperienceFilament\Pages\Overview::class,
                \Liberu\Foundation\AuditFilament\Pages\Overview::class,
            ],
        ];
    }
}
