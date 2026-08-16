<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `year_built` was a MySQL YEAR column, whose range is 1901-2155. A Victorian
 * or Georgian build year could not be stored at all: under this project's
 * sql_mode it was rejected outright, and without strict mode it would have
 * landed as 0000. That rules out a large share of UK housing stock, and "Built"
 * is one of the five facts shown on every card.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->reportZeroedYears();

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedSmallInteger('year_built')->nullable()->change();
        });
    }

    /**
     * A row already stored as 0000 lost its real year before this migration
     * ran, and nothing here can recover it. Say so rather than rewrite it to
     * null: someone has to go and look the year up.
     */
    private function reportZeroedYears(): void
    {
        $zeroed = DB::table('properties')->where('year_built', 0)->pluck('id');

        if ($zeroed->isEmpty()) {
            return;
        }

        $message = 'year_built is 0 on '.$zeroed->count().' propert'.($zeroed->count() === 1 ? 'y' : 'ies')
            .' (ids: '.$zeroed->implode(', ').'). A build year before 1901 was written to a YEAR column and lost.'
            .' These need looking up by hand; the migration leaves them as they are.';

        logger()->warning($message);

        if (app()->runningInConsole()) {
            echo PHP_EOL.'  '.$message.PHP_EOL;
        }
    }

    public function down(): void
    {
        // Anything before 1901 cannot survive the trip back, so say which rows
        // are about to be destroyed instead of silently zeroing them.
        $lost = DB::table('properties')->whereBetween('year_built', [1, 1900])->pluck('id');

        if ($lost->isNotEmpty()) {
            logger()->warning('Reverting year_built to YEAR will zero '.$lost->count()
                .' pre-1901 propert'.($lost->count() === 1 ? 'y' : 'ies').' (ids: '.$lost->implode(', ').').');
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->year('year_built')->nullable()->change();
        });
    }
};
