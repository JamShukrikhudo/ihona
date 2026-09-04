<?php

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\IdentityCoreApi\Http\Controllers\AuthTokenController;

// 'web' (not 'api'): these routes also establish a normal Laravel session
// via Auth::guard('web')->login() alongside issuing the Bearer token, so a
// visitor who logs in/registers through the Nuxt storefront is
// simultaneously recognized by the session-authenticated /app and /admin
// panels — see bootstrap/app.php for the matching CSRF exemption.
Route::post('api/v1/auth/token', [AuthTokenController::class, 'store'])
    ->middleware(['web', 'throttle:6,1'])
    ->name('identity.auth-token.store');

Route::delete('api/v1/auth/token', [AuthTokenController::class, 'destroy'])
    ->middleware(['web', 'auth:sanctum'])
    ->name('identity.auth-token.destroy');

Route::post('api/v1/auth/register', [AuthTokenController::class, 'register'])
    ->middleware(['web', 'throttle:6,1'])
    ->name('identity.auth-token.register');
