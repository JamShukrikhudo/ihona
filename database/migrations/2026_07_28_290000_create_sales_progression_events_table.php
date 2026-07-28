<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_progression_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_progression_id')->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->string('from_stage')->nullable();
            $table->string('to_stage')->nullable();
            $table->string('summary');
            $table->json('metadata')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['team_id', 'occurred_at']);
            $table->index(['sales_progression_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_progression_events');
    }
};
