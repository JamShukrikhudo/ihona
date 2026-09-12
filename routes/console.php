<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Liberu\RealEstate\Properties\Application\CheckPriceAlerts;
use Liberu\RealEstate\Properties\Application\CheckSavedSearchAlerts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// CheckPriceAlerts existed since 2026-08-30 but was never scheduled — price
// alerts silently never fired. CheckSavedSearchAlerts is new (see
// modules/real-estate-properties). Both only email when something actually
// matched, so hourly is cheap and keeps alerts close to real-time.
Schedule::call(fn () => app(CheckPriceAlerts::class)->handle())->name('check-price-alerts')->hourly()->onOneServer();
Schedule::call(fn () => app(CheckSavedSearchAlerts::class)->handle())->name('check-saved-search-alerts')->hourly()->onOneServer();

// Database + user-upload backups (see config/backup.php for what's actually
// included — deliberately not the codebase, which already lives in git).
Schedule::command('backup:run')->dailyAt('03:00')->onOneServer();
Schedule::command('backup:clean')->dailyAt('03:30')->onOneServer();
Schedule::command('backup:monitor')->dailyAt('04:00')->onOneServer();
