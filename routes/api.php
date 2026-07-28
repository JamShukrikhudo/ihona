<?php

use App\Http\Controllers\API\MetricsController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\VirtualStagingController;
use App\Http\Controllers\API\VRPropertyDesignController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PaymentController;
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
