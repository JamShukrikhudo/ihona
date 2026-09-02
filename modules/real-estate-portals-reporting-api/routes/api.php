<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PortalsReportingApi\Http\Controllers\DashboardLayoutController;
use Liberu\RealEstate\PortalsReportingApi\Http\Controllers\PortalReportController;
use Liberu\RealEstate\PortalsReportingApi\Http\Controllers\SavedReportController;

Route::prefix('api/v1/real-estate/saved-reports')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::post('/', [SavedReportController::class, 'store']);
    Route::get('/{savedReport}', [SavedReportController::class, 'show']);
    Route::get('/{savedReport}/run', [SavedReportController::class, 'run']);
    Route::get('/{savedReport}/export', [SavedReportController::class, 'export']);
});
Route::post('api/v1/real-estate/dashboard-layouts', [DashboardLayoutController::class, 'store'])->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency']);

Route::prefix('api/v1/real-estate/portals-and-reporting')->middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->group(function (): void {
    Route::get('/', [PortalReportController::class, 'index']);
    Route::post('/', [PortalReportController::class, 'store']);
    Route::get('/{portalReport}', [PortalReportController::class, 'show']);
    Route::match(['put', 'patch'], '/{portalReport}', [PortalReportController::class, 'update']);
    Route::post('/{portalReport}/transition/{status}', [PortalReportController::class, 'transition']);
    Route::patch('/{portalReport}/metrics/{metric}', [PortalReportController::class, 'metric'])->whereIn('metric', ['conversion', 'source', 'fee', 'time_to_complete', 'occupancy', 'compliance']);
    Route::delete('/{portalReport}', [PortalReportController::class, 'destroy']);
});
