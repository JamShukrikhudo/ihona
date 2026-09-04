<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Database + user-upload backups (see config/backup.php for what's actually
// included — deliberately not the codebase, which already lives in git).
Schedule::command('backup:run')->dailyAt('03:00')->onOneServer();
Schedule::command('backup:clean')->dailyAt('03:30')->onOneServer();
Schedule::command('backup:monitor')->dailyAt('04:00')->onOneServer();
