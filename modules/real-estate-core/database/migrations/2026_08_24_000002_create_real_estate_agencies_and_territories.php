<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_agencies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('code', 20);
            $table->boolean('active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });

        Schema::create('real_estate_territories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('code', 20);
            $table->json('boundary')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_territories');
        Schema::dropIfExists('real_estate_agencies');
    }
};
