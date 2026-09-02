<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PropertyManagementApi\Http\Controllers\InspectionController;
use Liberu\RealEstate\PropertyManagementApi\Http\Controllers\MaintenanceRequestController;
use Liberu\RealEstate\PropertyManagementApi\Http\Controllers\ManagementRecordController;
use Liberu\RealEstate\PropertyManagementApi\Http\Controllers\VendorQuoteController;
use Liberu\RealEstate\PropertyManagementApi\Http\Controllers\WorkOrderController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/property-management')->group(function (): void {
    Route::get('/', [ManagementRecordController::class, 'index']);
    Route::post('/', [ManagementRecordController::class, 'store']);
    Route::get('/{record}', [ManagementRecordController::class, 'show']);
    Route::patch('/{record}', [ManagementRecordController::class, 'update']);
    Route::patch('/{record}/details', [ManagementRecordController::class, 'updateDetails']);
    Route::post('/{record}/failure', [ManagementRecordController::class, 'recordFailure']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/work-orders')->group(function (): void {
    Route::get('/', [WorkOrderController::class, 'index']);
    Route::post('/', [WorkOrderController::class, 'store']);
    Route::get('/{workOrder}', [WorkOrderController::class, 'show']);
    Route::patch('/{workOrder}', [WorkOrderController::class, 'update']);
    Route::get('/{workOrder}/updates', [WorkOrderController::class, 'updates']);
    Route::post('/{workOrder}/updates', [WorkOrderController::class, 'storeUpdate']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/contractor-quotes')->group(function (): void {
    Route::get('/', [VendorQuoteController::class, 'index']);
    Route::post('/', [VendorQuoteController::class, 'store']);
    Route::get('/{contractorQuote}', [VendorQuoteController::class, 'show']);
    Route::patch('/{contractorQuote}', [VendorQuoteController::class, 'update']);
    Route::post('/{contractorQuote}/decision', [VendorQuoteController::class, 'decide']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/maintenance')->group(function (): void {
    Route::get('/', [MaintenanceRequestController::class, 'index']);
    Route::post('/', [MaintenanceRequestController::class, 'store']);
    Route::get('/{maintenance}', [MaintenanceRequestController::class, 'show']);
    Route::patch('/{maintenance}', [MaintenanceRequestController::class, 'update']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/inspections')->group(function (): void {
    Route::get('/', [InspectionController::class, 'index']);
    Route::post('/', [InspectionController::class, 'store']);
    Route::get('/{inspection}', [InspectionController::class, 'show']);
    Route::patch('/{inspection}', [InspectionController::class, 'update']);
});
