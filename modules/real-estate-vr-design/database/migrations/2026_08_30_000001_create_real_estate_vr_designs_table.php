<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_vr_designs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('vr_provider', 40)->default('mock');
            $table->json('design_data');
            $table->json('room_layout')->nullable();
            $table->json('furniture_items')->nullable();
            $table->json('materials')->nullable();
            $table->json('lighting')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('vr_scene_url')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_template')->default(false);
            $table->string('style')->nullable()->index();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['property_id', 'user_id']);
            $table->index(['team_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_vr_designs');
    }
};
