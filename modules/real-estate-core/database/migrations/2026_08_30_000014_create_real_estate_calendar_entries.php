<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_calendar_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('organiser_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
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
        Schema::dropIfExists('real_estate_calendar_entries');
    }
};
