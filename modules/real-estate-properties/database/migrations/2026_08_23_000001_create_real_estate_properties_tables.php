<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('real_estate_properties', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('address');
            $table->string('property_type', 40);
            $table->string('status', 40)->index();
            $table->json('characteristics')->nullable();
            $table->json('utilities')->nullable();
            $table->json('features')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('real_estate_property_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained('real_estate_properties')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->index();
            $table->string('event', 80);
            $table->json('changes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_property_history');
        Schema::dropIfExists('real_estate_properties');
    }
};
