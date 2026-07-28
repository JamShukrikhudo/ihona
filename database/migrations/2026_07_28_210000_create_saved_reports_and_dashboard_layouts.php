<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('type', 50);
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->string('chart_type', 30)->nullable();
            $table->boolean('is_shared')->default(true);
            $table->timestamps();
            $table->index(['team_id', 'type']);
        });

        Schema::create('dashboard_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('widgets');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_layouts');
        Schema::dropIfExists('saved_reports');
    }
};
