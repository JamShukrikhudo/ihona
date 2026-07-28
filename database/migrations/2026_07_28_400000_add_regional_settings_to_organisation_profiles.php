<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organisation_profiles', function (Blueprint $table) {
            $table->json('regional_settings')->nullable()->after('area_unit');
        });
    }

    public function down(): void
    {
        Schema::table('organisation_profiles', function (Blueprint $table) {
            $table->dropColumn('regional_settings');
        });
    }
};
