<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_media_documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->string('kind', 32);
            $table->string('path');
            $table->string('title')->nullable();
            $table->json('rights')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('retention_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'kind', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_media_documents');
    }
};
