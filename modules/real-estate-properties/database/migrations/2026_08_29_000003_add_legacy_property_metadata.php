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
            $table->timestamp('description_generated_at')->nullable()->after('description');
            $table->text('internal_notes')->nullable()->after('description_generated_at');
            $table->string('floor_plan_image', 2048)->nullable()->after('floor_plan_data');
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropColumn(['description_generated_at', 'internal_notes', 'floor_plan_image']);
        });
    }
};
