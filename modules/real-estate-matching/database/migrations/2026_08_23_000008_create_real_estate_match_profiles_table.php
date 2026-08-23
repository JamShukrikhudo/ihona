<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_match_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('party_id')->nullable()->index();
            $table->string('subject');
            $table->unsignedTinyInteger('score')->default(0);
            $table->json('requirements')->nullable();
            $table->json('affordability')->nullable();
            $table->json('preferences')->nullable();
            $table->json('alerts')->nullable();
            $table->json('feedback')->nullable();
            $table->json('exclusions')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_match_profiles');
    }
};
