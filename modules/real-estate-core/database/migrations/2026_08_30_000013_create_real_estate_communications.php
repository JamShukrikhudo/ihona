<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_communications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('related_type')->nullable();
            $table->string('related_id')->nullable();
            $table->string('channel');
            $table->string('direction')->default('outbound');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->string('status')->default('recorded');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'channel', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_communications');
    }
};
