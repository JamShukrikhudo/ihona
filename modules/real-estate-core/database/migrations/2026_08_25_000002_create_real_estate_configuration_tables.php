<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_terminology', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('locale', 12)->default('en');
            $table->string('key', 100);
            $table->string('value');
            $table->timestamps();
            $table->unique(['team_id', 'locale', 'key']);
        });

        Schema::create('real_estate_status_definitions', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('entity', 80);
            $table->string('key', 80);
            $table->string('label');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['team_id', 'entity', 'key']);
        });

        Schema::create('real_estate_audit_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id')->index();
            $table->string('actor_id')->nullable()->index();
            $table->string('event', 120);
            $table->string('subject_type', 160)->nullable();
            $table->string('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['team_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_audit_entries');
        Schema::dropIfExists('real_estate_status_definitions');
        Schema::dropIfExists('real_estate_terminology');
    }
};
