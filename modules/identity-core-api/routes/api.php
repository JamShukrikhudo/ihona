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

// Rehydrates {id, name, email, roles} from a stored Bearer token alone —
// the agent cabinet's auth store needs this on every page load/refresh
// (localStorage only ever kept the token, never the roles that came back
// from login), the storefront doesn't call it today but gets it for free.
Route::get('api/v1/auth/user', [AuthTokenController::class, 'show'])
    ->middleware(['auth:sanctum'])
    ->name('identity.auth-token.show');

Route::post('api/v1/auth/register', [AuthTokenController::class, 'register'])
    ->middleware(['web', 'throttle:6,1'])
    ->name('identity.auth-token.register');
