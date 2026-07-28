<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organiser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('reminder_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('status', 20)->default('scheduled');
            $table->json('attendee_user_ids')->nullable();
            $table->json('contact_ids')->nullable();
            $table->json('recurrence')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'starts_at']);
            $table->index(['team_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_entries');
    }
};
