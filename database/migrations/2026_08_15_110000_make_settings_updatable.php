<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Saving a setting inserted a second row instead of updating the first.
 *
 * spatie/laravel-settings writes with `upsert(..., ['group', 'name'], ...)`,
 * which needs a unique index on those two columns. This project's hand-rolled
 * `settings` table never had one — the package's own migration does. On MySQL
 * nothing ever conflicted, so every save appended a row and the reader, which
 * takes the first match, went on returning the original value: every edit made
 * in the admin panel was silently discarded. On SQLite it does not even get
 * that far, failing with "ON CONFLICT clause does not match any PRIMARY KEY or
 * UNIQUE constraint".
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->collapseDuplicates();

        Schema::table('settings', function (Blueprint $table) {
            // The reason the whole thing was broken.
            $table->unique(['group', 'name'], 'settings_group_name_unique');

            // The payload is JSON, and varchar(255) is not enough for it. The
            // copyright field alone accepts 500 characters in the admin panel,
            // which MySQL would have rejected outright under strict mode.
            $table->text('payload')->nullable()->change();
        });
    }

    /**
     * The newest row wins: it is what the administrator last saved, even though
     * the site has been showing the oldest all along. Anything collapsed is
     * reported, because for some settings this changes what the site displays.
     */
    private function collapseDuplicates(): void
    {
        $duplicates = DB::table('settings')
            ->select('group', 'name', DB::raw('COUNT(*) as total'), DB::raw('MAX(id) as keep'))
            ->groupBy('group', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        foreach ($duplicates as $duplicate) {
            DB::table('settings')
                ->where('group', $duplicate->group)
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->keep)
                ->delete();
        }

        $message = 'Collapsed duplicate settings rows for: '
            .$duplicates->map(fn ($d) => $d->group.'.'.$d->name.' ('.($d->total - 1).' discarded)')->implode(', ')
            .'. The most recently saved value was kept; the site had been showing the oldest.';

        logger()->warning($message);

        if (app()->runningInConsole()) {
            echo PHP_EOL.'  '.$message.PHP_EOL;
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_group_name_unique');
            $table->string('payload')->nullable()->change();
        });
    }
};
