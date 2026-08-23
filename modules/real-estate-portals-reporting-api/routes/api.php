<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PortalsReportingApi\Http\Controllers\PortalReportController;

Route::prefix('api/v1/real-estate/portal-reports')->middleware('api')->group(function (): void {
    Route::get('/', [PortalReportController::class, 'index']);
    Route::post('/', [PortalReportController::class, 'store']);
    Route::get('/{portalReport}', [PortalReportController::class, 'show']);
    Route::match(['put', 'patch'], '/{portalReport}', [PortalReportController::class, 'update']);
    Route::delete('/{portalReport}', [PortalReportController::class, 'destroy']);
});
