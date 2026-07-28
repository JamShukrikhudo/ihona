<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisation_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('agency_name');
            $table->string('logo_path')->nullable();
            $table->json('branding')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('address')->nullable();
            $table->json('operating_countries');
            $table->char('primary_country', 2);
            $table->char('currency', 3);
            $table->string('locale', 20);
            $table->string('language', 10);
            $table->string('timezone', 64);
            $table->string('date_format', 20);
            $table->string('measurement_system', 16);
            $table->string('area_unit', 16);
            $table->timestamp('setup_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisation_profiles');
    }
};
