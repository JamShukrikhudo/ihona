<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PortalsReportingApi\Http\Controllers\PortalReportController;

Route::prefix('api/v1/real-estate/portals-and-reporting')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PortalReportController::class, 'index']);
    Route::post('/', [PortalReportController::class, 'store']);
    Route::get('/{portalReport}', [PortalReportController::class, 'show']);
    Route::match(['put', 'patch'], '/{portalReport}', [PortalReportController::class, 'update']);
    Route::post('/{portalReport}/transition/{status}', [PortalReportController::class, 'transition']);
    Route::patch('/{portalReport}/metrics/{metric}', [PortalReportController::class, 'metric'])->whereIn('metric', ['conversion', 'source', 'fee', 'time_to_complete', 'occupancy', 'compliance']);
    Route::delete('/{portalReport}', [PortalReportController::class, 'destroy']);
});
