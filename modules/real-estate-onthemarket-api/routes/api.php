<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\OnTheMarketApi\Http\Controllers\OnTheMarketSyncController;

Route::prefix('api/v1/real-estate/integrations/onthemarket/syncs')->middleware('api')->group(function (): void {
    Route::get('/', [OnTheMarketSyncController::class, 'index']);
    Route::post('/', [OnTheMarketSyncController::class, 'store']);
    Route::get('/{onTheMarketSync}', [OnTheMarketSyncController::class, 'show']);
    Route::match(['put', 'patch'], '/{onTheMarketSync}', [OnTheMarketSyncController::class, 'update']);
    Route::delete('/{onTheMarketSync}', [OnTheMarketSyncController::class, 'destroy']);
});
