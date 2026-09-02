<?php

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\LettingsApi\Http\Controllers\LeaseAgreementController;
use Liberu\RealEstate\LettingsApi\Http\Controllers\LettingController;
use Liberu\RealEstate\LettingsApi\Http\Controllers\RentalApplicationController;
use Liberu\RealEstate\LettingsApi\Http\Controllers\RentalChargeController;

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/lettings')->group(function (): void {
    Route::get('/', [LettingController::class, 'index']);
    Route::post('/', [LettingController::class, 'store']);
    Route::get('/{letting}', [LettingController::class, 'show']);
    Route::patch('/{letting}', [LettingController::class, 'update']);
    Route::patch('/{letting}/details', [LettingController::class, 'updateDetails']);
    Route::post('/{letting}/failure', [LettingController::class, 'recordFailure']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/rental-charges')->group(function (): void {
    Route::get('/', [RentalChargeController::class, 'index']);
    Route::post('/', [RentalChargeController::class, 'store']);
    Route::get('/{rentalCharge}', [RentalChargeController::class, 'show']);
    Route::patch('/{rentalCharge}', [RentalChargeController::class, 'update']);
    Route::delete('/{rentalCharge}', [RentalChargeController::class, 'destroy']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/tenancy-agreements')->group(function (): void {
    Route::get('/', [LeaseAgreementController::class, 'index']);
    Route::post('/', [LeaseAgreementController::class, 'store']);
    Route::get('/{tenancyAgreement}', [LeaseAgreementController::class, 'show']);
    Route::patch('/{tenancyAgreement}', [LeaseAgreementController::class, 'update']);
    Route::post('/{tenancyAgreement}/renew', [LeaseAgreementController::class, 'renew']);
    Route::post('/{tenancyAgreement}/notice', [LeaseAgreementController::class, 'notice']);
});

Route::middleware(['api', 'auth:sanctum', 'throttle:api', 'api.idempotency'])->prefix('api/v1/real-estate/rental-applications')->group(function (): void {
    Route::get('/', [RentalApplicationController::class, 'index']);
    Route::post('/', [RentalApplicationController::class, 'store']);
    Route::get('/{rentalApplication}', [RentalApplicationController::class, 'show']);
    Route::patch('/{rentalApplication}', [RentalApplicationController::class, 'update']);
    Route::patch('/{rentalApplication}/screening', [RentalApplicationController::class, 'screening']);
    Route::post('/{rentalApplication}/decision', [RentalApplicationController::class, 'decide']);
});
