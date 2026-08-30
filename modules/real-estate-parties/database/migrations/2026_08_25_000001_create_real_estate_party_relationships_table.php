<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_party_relationships', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('related_party_id');
            $table->string('relationship', 60);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['team_id', 'party_id', 'related_party_id', 'relationship'], 'party_relationship_unique');
            $table->index(['team_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_party_relationships');
    }
};
