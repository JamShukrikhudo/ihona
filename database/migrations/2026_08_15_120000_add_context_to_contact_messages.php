<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The contact form has always asked which kind of enquiry this is, and the
 * listing cards now link here to ask about a specific property. Neither had
 * anywhere to go: the interest was silently dropped on every submission.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('interest')->nullable()->after('phone');
            $table->foreignId('property_id')->nullable()->after('interest')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('property_id');
            $table->dropColumn('interest');
        });
    }
};
