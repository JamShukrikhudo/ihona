<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_valuations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('party_id')->nullable()->index();
            $table->string('subject');
            $table->string('status', 32)->index();
            $table->decimal('valued_amount', 15, 2)->nullable();
            $table->decimal('fee_amount', 15, 2)->nullable();
            $table->json('comparable_data')->nullable();
            $table->json('recommendation')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_valuations');
    }
};
