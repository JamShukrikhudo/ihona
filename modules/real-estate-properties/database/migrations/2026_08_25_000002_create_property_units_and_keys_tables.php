<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_property_units', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('team_id')->index();
            $table->string('label', 80);
            $table->string('status', 40)->default('active');
            $table->unsignedSmallInteger('floor')->nullable();
            $table->unsignedSmallInteger('bedrooms')->nullable();
            $table->unsignedSmallInteger('bathrooms')->nullable();
            $table->decimal('area_sqft', 12, 2)->nullable();
            $table->json('characteristics')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['property_id', 'label']);
            $table->index(['team_id', 'status']);
        });
        Schema::create('real_estate_property_keys', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('team_id')->index();
            $table->string('key_reference', 80);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->string('status', 40)->default('held');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_property_keys');
        Schema::dropIfExists('real_estate_property_units');
    }
};
