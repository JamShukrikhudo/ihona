<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('provider');
            $table->string('name');
            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('last_checked_at')->nullable();
            $table->string('last_check_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['team_id', 'category', 'name']);
            $table->index(['team_id', 'category', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_integrations');
    }
};
