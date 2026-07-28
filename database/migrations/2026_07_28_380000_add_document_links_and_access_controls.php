<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_categories', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index(['team_id', 'name']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->nullableMorphs('documentable');
            $table->string('visibility', 20)->default('team');
            $table->json('allowed_user_ids')->nullable();
            $table->json('allowed_roles')->nullable();
            $table->index(['team_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'visibility']);
            $table->dropMorphs('documentable');
            $table->dropColumn(['visibility', 'allowed_user_ids', 'allowed_roles']);
        });

        Schema::table('document_categories', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'name']);
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
