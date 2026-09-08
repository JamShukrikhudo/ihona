<?php

use Illuminate\Support\Facades\Route;
use Liberu\Foundation\Identity\Socialstream\Http\Controllers\TelegramAuthController;

Route::middleware('web')
    ->get('/oauth/telegram/callback', [TelegramAuthController::class, 'callback'])
    ->name('oauth.telegram.callback');
