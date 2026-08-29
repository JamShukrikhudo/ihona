<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\MarketingApi\Http\Controllers\MarketingCampaignController;

Route::prefix('api/v1/real-estate/marketing')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [MarketingCampaignController::class, 'index']);
    Route::post('/', [MarketingCampaignController::class, 'store']);
    Route::get('/{marketingCampaign}', [MarketingCampaignController::class, 'show']);
    Route::match(['put', 'patch'], '/{marketingCampaign}', [MarketingCampaignController::class, 'update']);
    Route::post('/{marketingCampaign}/transition/{status}', [MarketingCampaignController::class, 'transition']);
    Route::patch('/{marketingCampaign}/{section}', [MarketingCampaignController::class, 'updateSection'])->whereIn('section', ['audience', 'content', 'schedule', 'metrics']);
    Route::delete('/{marketingCampaign}', [MarketingCampaignController::class, 'destroy']);
});
