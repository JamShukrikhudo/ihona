<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\MarketingApi\Http\Controllers\MarketingCampaignController;

Route::prefix('api/v1/real-estate/marketing-campaigns')->middleware('api')->group(function (): void {
    Route::get('/', [MarketingCampaignController::class, 'index']);
    Route::post('/', [MarketingCampaignController::class, 'store']);
    Route::get('/{marketingCampaign}', [MarketingCampaignController::class, 'show']);
    Route::match(['put', 'patch'], '/{marketingCampaign}', [MarketingCampaignController::class, 'update']);
    Route::delete('/{marketingCampaign}', [MarketingCampaignController::class, 'destroy']);
});
