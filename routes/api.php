<?php

use App\Http\Controllers\API\MetricsController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\V1\AccountingIntegrationController as ApiAccountingIntegrationController;
use App\Http\Controllers\API\V1\ApiTokenController;
use App\Http\Controllers\API\V1\AutomationController as ApiAutomationController;
use App\Http\Controllers\API\V1\BranchController as ApiBranchController;
use App\Http\Controllers\API\V1\BuyerController as ApiBuyerController;
use App\Http\Controllers\API\V1\CalendarController as ApiCalendarController;
use App\Http\Controllers\API\V1\CalendarEntryController as ApiCalendarEntryController;
use App\Http\Controllers\API\V1\CommunicationController as ApiCommunicationController;
use App\Http\Controllers\API\V1\CompanyController as ApiCompanyController;
use App\Http\Controllers\API\V1\ComplianceDocumentController as ApiComplianceDocumentController;
use App\Http\Controllers\API\V1\ComplianceItemController as ApiComplianceItemController;
use App\Http\Controllers\API\V1\ContactController as ApiContactController;
use App\Http\Controllers\API\V1\ContactRelationshipController as ApiContactRelationshipController;
use App\Http\Controllers\API\V1\ContractorController as ApiContractorController;
use App\Http\Controllers\API\V1\DashboardLayoutController as ApiDashboardLayoutController;
use App\Http\Controllers\API\V1\DepartmentController as ApiDepartmentController;
use App\Http\Controllers\API\V1\DocumentController as ApiDocumentController;
use App\Http\Controllers\API\V1\DocumentWorkflowController as ApiDocumentWorkflowController;
use App\Http\Controllers\API\V1\EmailCampaignController as ApiEmailCampaignController;
use App\Http\Controllers\API\V1\InspectionController as ApiInspectionController;
use App\Http\Controllers\API\V1\MaintenanceController as ApiMaintenanceController;
use App\Http\Controllers\API\V1\MarketingAssetController as ApiMarketingAssetController;
use App\Http\Controllers\API\V1\NotificationController as ApiNotificationController;
use App\Http\Controllers\API\V1\OfferController as ApiOfferController;
use App\Http\Controllers\API\V1\PermissionController as ApiPermissionController;
use App\Http\Controllers\API\V1\PortalIntegrationController as ApiPortalIntegrationController;
use App\Http\Controllers\API\V1\PropertyController as ApiPropertyController;
use App\Http\Controllers\API\V1\PropertyMatchController as ApiPropertyMatchController;
use App\Http\Controllers\API\V1\PropertyMediaController as ApiPropertyMediaController;
use App\Http\Controllers\API\V1\PublicWebsiteController as ApiPublicWebsiteController;
use App\Http\Controllers\API\V1\RentalApplicationController as ApiRentalApplicationController;
use App\Http\Controllers\API\V1\ReportController as ApiReportController;
use App\Http\Controllers\API\V1\SalesProgressionController as ApiSalesProgressionController;
use App\Http\Controllers\API\V1\SavedReportController as ApiSavedReportController;
use App\Http\Controllers\API\V1\SearchController as ApiSearchController;
use App\Http\Controllers\API\V1\ServiceIntegrationController as ApiServiceIntegrationController;
use App\Http\Controllers\API\V1\SetupController as ApiSetupController;
use App\Http\Controllers\API\V1\StaffController as ApiStaffController;
use App\Http\Controllers\API\V1\TaskCollaborationController as ApiTaskCollaborationController;
use App\Http\Controllers\API\V1\TaskController as ApiTaskController;
use App\Http\Controllers\API\V1\TenancyAgreementController as ApiTenancyAgreementController;
use App\Http\Controllers\API\V1\TenantController as ApiTenantController;
use App\Http\Controllers\API\V1\ValuationController as ApiValuationController;
use App\Http\Controllers\API\V1\VendorQuoteController as ApiVendorQuoteController;
use App\Http\Controllers\API\V1\ViewingController as ApiViewingController;
use App\Http\Controllers\API\V1\WorkOrderController as ApiWorkOrderController;
use App\Http\Controllers\API\VirtualStagingController;
use App\Http\Controllers\API\VRPropertyDesignController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\ApplyOrganisationLocale;
use App\Http\Middleware\RequireAgencyPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/payments/webhook', [PaymentController::class, 'handleWebhook'])
    ->middleware('throttle:60,1');

Route::prefix('v1/public/agencies/{team}')->middleware('throttle:60,1')->name('api.v1.public.')->group(function () {
    Route::get('properties', [ApiPublicWebsiteController::class, 'properties'])->name('properties');
    Route::get('properties/{property}', [ApiPublicWebsiteController::class, 'property'])->name('property');
    Route::get('branches', [ApiPublicWebsiteController::class, 'branches'])->name('branches');
    Route::get('staff', [ApiPublicWebsiteController::class, 'staff'])->name('staff');
});

// Public metrics endpoint (for k8s monitoring/control-panel)
Route::get('/metrics', [MetricsController::class, 'index'])->name('api.metrics');

// Module management (admin only)
Route::middleware(['auth:sanctum', 'role:super_admin|admin'])->prefix('modules')->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('api.modules.index');
    Route::get('/status', [ModuleController::class, 'status'])->name('api.modules.status');
    Route::get('/{name}', [ModuleController::class, 'show'])->name('api.modules.show');
    Route::post('/{name}/enable', [ModuleController::class, 'enable'])->name('api.modules.enable');
    Route::post('/{name}/disable', [ModuleController::class, 'disable'])->name('api.modules.disable');
});

// Public News API Routes
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/latest', [NewsController::class, 'latest']);
    Route::get('/featured', [NewsController::class, 'featured']);
    Route::get('/{slug}', [NewsController::class, 'show']);
});

// Public Community Events API Routes
Route::prefix('community-events')->group(function () {
    Route::get('/', [CommunityEventController::class, 'index']);
    Route::get('/{id}', [CommunityEventController::class, 'show']);
});

// Property-specific community events route
Route::get('/properties/{propertyId}/community-events', [CommunityEventController::class, 'propertyEvents']);

// Chatbot API Routes (rate-limited)
Route::middleware('throttle:chatbot')->prefix('chatbot')->group(function () {
    Route::post('/start', [ChatbotController::class, 'startConversation']);
    Route::post('/message', [ChatbotController::class, 'sendMessage']);
    Route::get('/history/{sessionId}', [ChatbotController::class, 'getHistory']);
    Route::post('/escalate', [ChatbotController::class, 'escalate']);
    Route::post('/close', [ChatbotController::class, 'closeConversation']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->middleware([ApplyOrganisationLocale::class, RequireAgencyPermission::class])->name('api.v1.')->group(function () {
        Route::get('permissions/catalog', [ApiPermissionController::class, 'catalog'])->name('permissions.catalog');
        Route::get('permissions/members', [ApiPermissionController::class, 'members'])->name('permissions.members');
        Route::get('permissions/audits', [ApiPermissionController::class, 'audits'])->name('permissions.audits');
        Route::put('permissions/members/{member}', [ApiPermissionController::class, 'update'])->name('permissions.update');
        Route::get('api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::delete('api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
        Route::get('calendar', ApiCalendarController::class)->name('calendar');
        Route::apiResource('calendar-entries', ApiCalendarEntryController::class);
        Route::apiResource('accounting-integrations', ApiAccountingIntegrationController::class);
        Route::post('accounting-integrations/{accounting_integration}/sync', [ApiAccountingIntegrationController::class, 'sync'])->name('accounting-integrations.sync');
        Route::get('accounting-sync-runs', [ApiAccountingIntegrationController::class, 'runs'])->name('accounting-sync-runs.index');
        Route::get('accounting-summary', [ApiAccountingIntegrationController::class, 'summary'])->name('accounting-summary');
        Route::get('accounting-links', [ApiAccountingIntegrationController::class, 'links'])->name('accounting-links.index');
        Route::post('accounting-links', [ApiAccountingIntegrationController::class, 'storeLink'])->name('accounting-links.store');
        Route::patch('accounting-links/{accounting_link}', [ApiAccountingIntegrationController::class, 'updateLink'])->name('accounting-links.update');
        Route::delete('accounting-links/{accounting_link}', [ApiAccountingIntegrationController::class, 'destroyLink'])->name('accounting-links.destroy');
        Route::get('setup/options', [ApiSetupController::class, 'options'])->name('setup.options');
        Route::get('setup/status', [ApiSetupController::class, 'status'])->name('setup.status');
        Route::put('setup', [ApiSetupController::class, 'complete'])->name('setup.complete');
        Route::get('service-integrations/options', [ApiServiceIntegrationController::class, 'options'])->name('service-integrations.options');
        Route::apiResource('service-integrations', ApiServiceIntegrationController::class);
        Route::post('service-integrations/{service_integration}/check', [ApiServiceIntegrationController::class, 'check'])->name('service-integrations.check');
        Route::get('search', ApiSearchController::class)->name('search');
        Route::get('reports/dashboard', [ApiReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('reports/pipeline', [ApiReportController::class, 'pipeline'])->name('reports.pipeline');
        Route::apiResource('saved-reports', ApiSavedReportController::class);
        Route::get('saved-reports/{savedReport}/run', [ApiReportController::class, 'run'])->name('saved-reports.run');
        Route::get('saved-reports/{savedReport}/export', [ApiReportController::class, 'export'])->name('saved-reports.export');
        Route::apiResource('dashboard-layouts', ApiDashboardLayoutController::class);
        Route::apiResource('contacts', ApiContactController::class);
        Route::get('contacts/{contact}/relationships', [ApiContactRelationshipController::class, 'index'])->name('contacts.relationships.index');
        Route::post('contacts/{contact}/relationships', [ApiContactRelationshipController::class, 'store'])->name('contacts.relationships.store');
        Route::patch('contacts/{contact}/relationships/{relationship}', [ApiContactRelationshipController::class, 'update'])->name('contacts.relationships.update');
        Route::delete('contacts/{contact}/relationships/{relationship}', [ApiContactRelationshipController::class, 'destroy'])->name('contacts.relationships.destroy');
        Route::apiResource('companies', ApiCompanyController::class);
        Route::apiResource('communications', ApiCommunicationController::class);
        Route::apiResource('contractors', ApiContractorController::class);
        Route::apiResource('contractor-quotes', ApiVendorQuoteController::class)
            ->parameters(['contractor-quotes' => 'contractor_quote']);
        Route::post('contractor-quotes/{contractor_quote}/decision', [ApiVendorQuoteController::class, 'decide'])->name('contractor-quotes.decision');
        Route::apiResource('work-orders', ApiWorkOrderController::class);
        Route::get('work-orders/{work_order}/updates', [ApiWorkOrderController::class, 'updates'])->name('work-orders.updates.index');
        Route::post('work-orders/{work_order}/updates', [ApiWorkOrderController::class, 'storeUpdate'])->name('work-orders.updates.store');
        Route::apiResource('compliance-items', ApiComplianceItemController::class);
        Route::get('compliance-items/{compliance_item}/documents', [ApiComplianceDocumentController::class, 'index'])->name('compliance-items.documents.index');
        Route::post('compliance-items/{compliance_item}/documents', [ApiComplianceDocumentController::class, 'store'])->name('compliance-items.documents.store');
        Route::get('compliance-items/{compliance_item}/documents/{document}/download', [ApiComplianceDocumentController::class, 'download'])->name('compliance-items.documents.download');
        Route::patch('compliance-items/{compliance_item}/documents/{document}/verify', [ApiComplianceDocumentController::class, 'verify'])->name('compliance-items.documents.verify');
        Route::delete('compliance-items/{compliance_item}/documents/{document}', [ApiComplianceDocumentController::class, 'destroy'])->name('compliance-items.documents.destroy');
        Route::apiResource('branches', ApiBranchController::class);
        Route::apiResource('departments', ApiDepartmentController::class);
        Route::apiResource('staff', ApiStaffController::class)->parameters(['staff' => 'staff']);
        Route::apiResource('automations', ApiAutomationController::class);
        Route::post('automations/{automation}/run', [ApiAutomationController::class, 'run'])->name('automations.run');
        Route::get('automation-runs', [ApiAutomationController::class, 'runs'])->name('automation-runs.index');
        Route::apiResource('buyers', ApiBuyerController::class);
        Route::apiResource('documents', ApiDocumentController::class);
        Route::get('documents/{document}/versions', [ApiDocumentWorkflowController::class, 'versions'])->name('documents.versions.index');
        Route::post('documents/{document}/versions', [ApiDocumentWorkflowController::class, 'storeVersion'])->name('documents.versions.store');
        Route::get('documents/{document}/versions/{version}/download', [ApiDocumentWorkflowController::class, 'download'])->name('documents.versions.download');
        Route::get('documents/{document}/signatures', [ApiDocumentWorkflowController::class, 'signatures'])->name('documents.signatures.index');
        Route::post('documents/{document}/signatures', [ApiDocumentWorkflowController::class, 'sign'])->name('documents.signatures.store');
        Route::get('marketing-assets/properties/{property}/brochure', [ApiMarketingAssetController::class, 'brochure'])->name('marketing-assets.brochure');
        Route::get('marketing-assets/properties/{property}/window-card', [ApiMarketingAssetController::class, 'windowCard'])->name('marketing-assets.window-card');
        Route::get('marketing-assets/properties/{property}/qr-code', [ApiMarketingAssetController::class, 'qrCode'])->name('marketing-assets.qr-code');
        Route::get('marketing-assets/properties/{property}/social', [ApiMarketingAssetController::class, 'social'])->name('marketing-assets.social');
        Route::apiResource('email-campaigns', ApiEmailCampaignController::class);
        Route::get('email-campaigns/{emailCampaign}/preview', [ApiEmailCampaignController::class, 'preview'])->name('email-campaigns.preview');
        Route::post('email-campaigns/{emailCampaign}/schedule', [ApiEmailCampaignController::class, 'schedule'])->name('email-campaigns.schedule');
        Route::post('email-campaigns/{emailCampaign}/cancel', [ApiEmailCampaignController::class, 'cancel'])->name('email-campaigns.cancel');
        Route::get('email-campaigns/{emailCampaign}/metrics', [ApiEmailCampaignController::class, 'metrics'])->name('email-campaigns.metrics');
        Route::apiResource('inspections', ApiInspectionController::class);
        Route::apiResource('maintenance', ApiMaintenanceController::class);
        Route::apiResource('tasks', ApiTaskController::class);
        Route::get('tasks/{task}/comments', [ApiTaskCollaborationController::class, 'comments'])->name('tasks.comments.index');
        Route::post('tasks/{task}/comments', [ApiTaskCollaborationController::class, 'storeComment'])->name('tasks.comments.store');
        Route::delete('tasks/{task}/comments/{comment}', [ApiTaskCollaborationController::class, 'destroyComment'])->name('tasks.comments.destroy');
        Route::get('tasks/{task}/attachments', [ApiTaskCollaborationController::class, 'attachments'])->name('tasks.attachments.index');
        Route::post('tasks/{task}/attachments', [ApiTaskCollaborationController::class, 'storeAttachment'])->name('tasks.attachments.store');
        Route::delete('tasks/{task}/attachments/{attachment}', [ApiTaskCollaborationController::class, 'destroyAttachment'])->name('tasks.attachments.destroy');
        Route::patch('tasks/{task}/checklist/{item}', [ApiTaskCollaborationController::class, 'checklist'])->name('tasks.checklist.update');
        Route::apiResource('tenants', ApiTenantController::class);
        Route::apiResource('rental-applications', ApiRentalApplicationController::class);
        Route::patch('rental-applications/{rental_application}/screening', [ApiRentalApplicationController::class, 'screening'])->name('rental-applications.screening');
        Route::post('rental-applications/{rental_application}/decision', [ApiRentalApplicationController::class, 'decide'])->name('rental-applications.decision');
        Route::apiResource('tenancy-agreements', ApiTenancyAgreementController::class);
        Route::post('tenancy-agreements/{tenancy_agreement}/renew', [ApiTenancyAgreementController::class, 'renew'])->name('tenancy-agreements.renew');
        Route::post('tenancy-agreements/{tenancy_agreement}/notice', [ApiTenancyAgreementController::class, 'notice'])->name('tenancy-agreements.notice');
        Route::apiResource('offers', ApiOfferController::class);
        Route::get('offers/{offer}/timeline', [ApiOfferController::class, 'timeline'])->name('offers.timeline');
        Route::apiResource('portal-integrations', ApiPortalIntegrationController::class);
        Route::post('portal-integrations/{portal_integration}/sync', [ApiPortalIntegrationController::class, 'sync'])->name('portal-integrations.sync');
        Route::get('portal-sync-runs', [ApiPortalIntegrationController::class, 'runs'])->name('portal-sync-runs.index');
        Route::put('portal-integrations/{portal_integration}/properties/{property}', [ApiPortalIntegrationController::class, 'publish'])->name('portal-integrations.properties.publish');
        Route::delete('portal-integrations/{portal_integration}/properties/{property}', [ApiPortalIntegrationController::class, 'unpublish'])->name('portal-integrations.properties.unpublish');
        Route::get('notifications', [ApiNotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/preferences', [ApiNotificationController::class, 'preferences'])->name('notifications.preferences');
        Route::put('notifications/preferences', [ApiNotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
        Route::get('notifications/deliveries', [ApiNotificationController::class, 'deliveries'])->name('notifications.deliveries');
        Route::patch('notifications/{notification}/read', [ApiNotificationController::class, 'read'])->name('notifications.read');
        Route::post('notifications/read-all', [ApiNotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::apiResource('sales-progressions', ApiSalesProgressionController::class);
        Route::get('sales-progressions/{sales_progression}/timeline', [ApiSalesProgressionController::class, 'timeline'])->name('sales-progressions.timeline');
        Route::patch('sales-progressions/{sales_progression}/stage', [ApiSalesProgressionController::class, 'stage'])->name('sales-progressions.stage');
        Route::patch('sales-progressions/{sales_progression}/checklist/{item}', [ApiSalesProgressionController::class, 'checklist'])->name('sales-progressions.checklist');
        Route::post('sales-progressions/{sales_progression}/memorandum', [ApiSalesProgressionController::class, 'memorandum'])->name('sales-progressions.memorandum');
        Route::apiResource('valuations', ApiValuationController::class);
        Route::apiResource('viewings', ApiViewingController::class);
        Route::post('viewings/{viewing}/confirmation', [ApiViewingController::class, 'sendConfirmation'])->name('viewings.confirmation');
        Route::post('viewings/{viewing}/reminder', [ApiViewingController::class, 'sendReminder'])->name('viewings.reminder');
        Route::get('viewings/{viewing}/feedback', [ApiViewingController::class, 'feedback'])->name('viewings.feedback.index');
        Route::post('viewings/{viewing}/feedback', [ApiViewingController::class, 'requestFeedback'])->name('viewings.feedback.request');
        Route::patch('viewings/{viewing}/feedback/{feedback}', [ApiViewingController::class, 'submitFeedback'])->name('viewings.feedback.submit');
        Route::apiResource('properties', ApiPropertyController::class);
        Route::get('properties/{property}/media', [ApiPropertyMediaController::class, 'index'])->name('property-media.index');
        Route::post('properties/{property}/media', [ApiPropertyMediaController::class, 'store'])->name('property-media.store');
        Route::put('properties/{property}/media/reorder', [ApiPropertyMediaController::class, 'reorder'])->name('property-media.reorder');
        Route::get('properties/{property}/media/{medium}', [ApiPropertyMediaController::class, 'show'])->name('property-media.show');
        Route::patch('properties/{property}/media/{medium}', [ApiPropertyMediaController::class, 'update'])->name('property-media.update');
        Route::get('properties/{property}/media/{medium}/download', [ApiPropertyMediaController::class, 'download'])->name('property-media.download');
        Route::delete('properties/{property}/media/{medium}', [ApiPropertyMediaController::class, 'destroy'])->name('property-media.destroy');
        Route::get('property-matches', [ApiPropertyMatchController::class, 'index'])->name('property-matches.index');
        Route::patch('property-matches/{property_match}', [ApiPropertyMatchController::class, 'update'])->name('property-matches.update');
        Route::post('buyers/{buyer}/generate-matches', [ApiPropertyMatchController::class, 'forBuyer'])->name('buyers.generate-matches');
        Route::post('tenants/{tenant}/generate-matches', [ApiPropertyMatchController::class, 'forTenant'])->name('tenants.generate-matches');
        Route::post('properties/{property}/generate-matches', [ApiPropertyMatchController::class, 'forProperty'])->name('properties.generate-matches');
    });

    // Virtual Staging API Routes
    Route::prefix('properties/{property}')->group(function () {
        Route::post('images/upload', [VirtualStagingController::class, 'uploadImage']);
        Route::get('images', [VirtualStagingController::class, 'getPropertyImages']);
    });

    Route::prefix('images')->group(function () {
        Route::post('{image}/stage', [VirtualStagingController::class, 'stageImage']);
        Route::delete('{image}', [VirtualStagingController::class, 'deleteImage']);
    });

    Route::get('staging/styles', [VirtualStagingController::class, 'getStagingStyles']);

    // VR Property Design API Routes
    Route::prefix('vr-design')->group(function () {
        Route::get('styles', [VRPropertyDesignController::class, 'getStyles']);
        Route::get('furniture-categories', [VRPropertyDesignController::class, 'getFurnitureCategories']);
        Route::get('room-types', [VRPropertyDesignController::class, 'getRoomTypes']);
        Route::get('devices', [VRPropertyDesignController::class, 'getSupportedDevices']);
        Route::get('templates', [VRPropertyDesignController::class, 'getTemplates']);
    });

    Route::prefix('properties/{property}')->group(function () {
        Route::post('vr-designs', [VRPropertyDesignController::class, 'createDesign']);
        Route::get('vr-designs', [VRPropertyDesignController::class, 'getPropertyDesigns']);
    });

    Route::prefix('vr-designs')->group(function () {
        Route::get('{design}', [VRPropertyDesignController::class, 'getDesign']);
        Route::put('{design}', [VRPropertyDesignController::class, 'updateDesign']);
        Route::delete('{design}', [VRPropertyDesignController::class, 'deleteDesign']);
        Route::post('{design}/furniture', [VRPropertyDesignController::class, 'addFurniture']);
        Route::delete('{design}/furniture/{furnitureId}', [VRPropertyDesignController::class, 'removeFurniture']);
        Route::post('{design}/clone', [VRPropertyDesignController::class, 'cloneDesign']);
        Route::post('{design}/thumbnail', [VRPropertyDesignController::class, 'uploadThumbnail']);
        Route::get('{design}/export', [VRPropertyDesignController::class, 'exportDesign']);
    });

    // Wishlist/Favorites routes
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{propertyId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/check/{propertyId}', [FavoriteController::class, 'check']);
});
