<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_zoopla_syncs', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['team_id', 'listing_id']);
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_zoopla_syncs');
    }
};
