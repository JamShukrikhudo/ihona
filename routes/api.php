<?php

use App\Http\Controllers\API\MetricsController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\V1\CommunicationController as ApiCommunicationController;
use App\Http\Controllers\API\V1\CompanyController as ApiCompanyController;
use App\Http\Controllers\API\V1\ContactController as ApiContactController;
use App\Http\Controllers\API\V1\InspectionController as ApiInspectionController;
use App\Http\Controllers\API\V1\BranchController as ApiBranchController;
use App\Http\Controllers\API\V1\BuyerController as ApiBuyerController;
use App\Http\Controllers\API\V1\DocumentController as ApiDocumentController;
use App\Http\Controllers\API\V1\MaintenanceController as ApiMaintenanceController;
use App\Http\Controllers\API\V1\PropertyMatchController as ApiPropertyMatchController;
use App\Http\Controllers\API\V1\OfferController as ApiOfferController;
use App\Http\Controllers\API\V1\PropertyController as ApiPropertyController;
use App\Http\Controllers\API\V1\PublicWebsiteController as ApiPublicWebsiteController;
use App\Http\Controllers\API\V1\ReportController as ApiReportController;
use App\Http\Controllers\API\V1\SalesProgressionController as ApiSalesProgressionController;
use App\Http\Controllers\API\V1\SearchController as ApiSearchController;
use App\Http\Controllers\API\V1\SetupController as ApiSetupController;
use App\Http\Controllers\API\V1\TaskController as ApiTaskController;
use App\Http\Controllers\API\V1\TenancyAgreementController as ApiTenancyAgreementController;
use App\Http\Controllers\API\V1\TenantController as ApiTenantController;
use App\Http\Controllers\API\V1\ValuationController as ApiValuationController;
use App\Http\Controllers\API\V1\ViewingController as ApiViewingController;
use App\Http\Controllers\API\VirtualStagingController;
use App\Http\Controllers\API\VRPropertyDesignController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\ApplyOrganisationLocale;
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
    Route::prefix('v1')->middleware(ApplyOrganisationLocale::class)->name('api.v1.')->group(function () {
        Route::get('setup/options', [ApiSetupController::class, 'options'])->name('setup.options');
        Route::get('setup/status', [ApiSetupController::class, 'status'])->name('setup.status');
        Route::put('setup', [ApiSetupController::class, 'complete'])->name('setup.complete');
        Route::get('search', ApiSearchController::class)->name('search');
        Route::get('reports/dashboard', [ApiReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('reports/pipeline', [ApiReportController::class, 'pipeline'])->name('reports.pipeline');
        Route::apiResource('contacts', ApiContactController::class);
        Route::apiResource('companies', ApiCompanyController::class);
        Route::apiResource('communications', ApiCommunicationController::class);
        Route::apiResource('branches', ApiBranchController::class);
        Route::apiResource('buyers', ApiBuyerController::class);
        Route::apiResource('documents', ApiDocumentController::class);
        Route::apiResource('inspections', ApiInspectionController::class);
        Route::apiResource('maintenance', ApiMaintenanceController::class);
        Route::apiResource('tasks', ApiTaskController::class);
        Route::apiResource('tenants', ApiTenantController::class);
        Route::apiResource('tenancy-agreements', ApiTenancyAgreementController::class);
        Route::post('tenancy-agreements/{tenancy_agreement}/renew', [ApiTenancyAgreementController::class, 'renew'])->name('tenancy-agreements.renew');
        Route::post('tenancy-agreements/{tenancy_agreement}/notice', [ApiTenancyAgreementController::class, 'notice'])->name('tenancy-agreements.notice');
        Route::apiResource('offers', ApiOfferController::class);
        Route::apiResource('sales-progressions', ApiSalesProgressionController::class);
        Route::apiResource('valuations', ApiValuationController::class);
        Route::apiResource('viewings', ApiViewingController::class);
        Route::apiResource('properties', ApiPropertyController::class);
        Route::get('property-matches', [ApiPropertyMatchController::class, 'index'])->name('property-matches.index');
        Route::patch('property-matches/{property_match}', [ApiPropertyMatchController::class, 'update'])->name('property-matches.update');
        Route::post('buyers/{buyer}/generate-matches', [ApiPropertyMatchController::class, 'forBuyer'])->name('buyers.generate-matches');
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
