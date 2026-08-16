<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('relationship');
            $table->string('inverse_relationship');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['team_id', 'contact_id', 'related_contact_id'], 'contact_relationships_unique');
            $table->index(['team_id', 'related_contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_relationships');
    }
};
