<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\ZooplaApi\Http\Controllers\ZooplaSyncController;

Route::prefix('api/v1/real-estate/integrations/zoopla/syncs')->middleware('api')->group(function (): void {
    Route::get('/', [ZooplaSyncController::class, 'index']);
    Route::post('/', [ZooplaSyncController::class, 'store']);
    Route::get('/{zooplaSync}', [ZooplaSyncController::class, 'show']);
    Route::match(['put', 'patch'], '/{zooplaSync}', [ZooplaSyncController::class, 'update']);
    Route::delete('/{zooplaSync}', [ZooplaSyncController::class, 'destroy']);
});
