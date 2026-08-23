<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\RightmoveApi\Http\Controllers\RightmoveSyncController;

Route::prefix('api/v1/real-estate/integrations/rightmove/syncs')->middleware('api')->group(function (): void {
    Route::get('/', [RightmoveSyncController::class, 'index']);
    Route::post('/', [RightmoveSyncController::class, 'store']);
    Route::get('/{rightmoveSync}', [RightmoveSyncController::class, 'show']);
    Route::match(['put', 'patch'], '/{rightmoveSync}', [RightmoveSyncController::class, 'update']);
    Route::delete('/{rightmoveSync}', [RightmoveSyncController::class, 'destroy']);
});
