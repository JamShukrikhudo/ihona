<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The booking form has always asked for an email and validated it, and there
 * has never been anywhere to put it. A guest booking a viewing has no account,
 * so this was the only way to reach them and it was thrown away on every
 * submission.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('email')->nullable()->after('contact');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
