<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropColumn(['ar_tour_enabled', 'ar_tour_settings', 'ar_placement_guide', 'ar_model_scale']);
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->boolean('ar_tour_enabled')->default(false)->after('live_tour_available');
            $table->json('ar_tour_settings')->nullable()->after('ar_tour_enabled');
            $table->string('ar_placement_guide')->nullable()->after('ar_tour_settings');
            $table->decimal('ar_model_scale', 8, 4)->nullable()->after('ar_placement_guide');
        });
    }
};
