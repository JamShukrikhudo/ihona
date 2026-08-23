<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_viewings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('party_id')->nullable()->index();
            $table->string('subject');
            $table->string('status', 32)->index();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->json('access')->nullable();
            $table->json('accompaniment')->nullable();
            $table->json('reminders')->nullable();
            $table->json('feedback')->nullable();
            $table->boolean('no_show')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_viewings');
    }
};
