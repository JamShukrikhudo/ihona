<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_sales_progressions', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('offer_id')->nullable();
            $table->string('subject');
            $table->string('status')->default('in_progress');
            $table->json('milestones')->nullable();
            $table->json('chain')->nullable();
            $table->json('professionals')->nullable();
            $table->json('completion_controls')->nullable();
            $table->dateTime('exchanged_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_sales_progressions');
    }
};
