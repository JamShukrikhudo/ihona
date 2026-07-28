<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('status')->default('scheduled');
            $table->dateTime('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('areas')->nullable();
            $table->json('photos')->nullable();
            $table->json('damage_reports')->nullable();
            $table->json('signatures')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'scheduled_at']);
        });

        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('communicable');
            $table->string('channel');
            $table->string('direction')->default('outbound');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->string('status')->default('recorded');
            $table->json('metadata')->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'channel', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
        Schema::dropIfExists('inspections');
    }
};
