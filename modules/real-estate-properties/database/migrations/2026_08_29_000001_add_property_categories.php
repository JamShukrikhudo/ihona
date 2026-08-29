<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_property_categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->timestamps();

            $table->unique(['team_id', 'slug']);
        });

        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->foreignId('property_category_id')
                ->nullable()
                ->after('property_type')
                ->constrained('real_estate_property_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('property_category_id');
        });

        Schema::dropIfExists('real_estate_property_categories');
    }
};
