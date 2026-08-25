<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\ZooplaApi\Http\Controllers\ZooplaSyncController;

Route::prefix('api/v1/real-estate/integrations/zoopla/syncs')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [ZooplaSyncController::class, 'index']);
    Route::post('/', [ZooplaSyncController::class, 'store']);
    Route::post('/{zooplaSync}/sync', [ZooplaSyncController::class, 'sync']);
    Route::get('/{zooplaSync}', [ZooplaSyncController::class, 'show']);
    Route::match(['put', 'patch'], '/{zooplaSync}', [ZooplaSyncController::class, 'update']);
    Route::delete('/{zooplaSync}', [ZooplaSyncController::class, 'destroy']);
});
