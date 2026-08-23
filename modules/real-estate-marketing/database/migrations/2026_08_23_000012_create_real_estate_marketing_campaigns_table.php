<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_marketing_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('name');
            $table->string('channel');
            $table->string('status')->default('draft');
            $table->json('audience')->nullable();
            $table->json('content')->nullable();
            $table->json('schedule')->nullable();
            $table->json('metrics')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_marketing_campaigns');
    }
};
