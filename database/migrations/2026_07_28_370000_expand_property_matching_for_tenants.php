<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('team_id')->constrained()->nullOnDelete();
            $table->json('search_criteria')->nullable()->after('user_id');
            $table->string('status', 20)->default('active')->after('search_criteria');
        });

        Schema::table('property_matches', function (Blueprint $table) {
            $table->dropUnique(['buyer_id', 'property_id']);
            $table->foreignId('tenant_id')->nullable()->after('buyer_id')->constrained()->cascadeOnDelete();
            $table->decimal('school_match', 5, 2)->nullable()->after('type_match');
            $table->decimal('transport_match', 5, 2)->nullable()->after('school_match');
            $table->decimal('distance_km', 8, 2)->nullable()->after('transport_match');
            $table->string('availability', 30)->nullable()->after('distance_km');
            $table->unique(['buyer_id', 'property_id']);
            $table->unique(['tenant_id', 'property_id']);
        });

        Schema::table('property_matches', function (Blueprint $table) {
            $table->foreignId('buyer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('property_matches')->whereNull('buyer_id')->delete();

        Schema::table('property_matches', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'property_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn([
                'tenant_id', 'school_match', 'transport_match', 'distance_km', 'availability',
            ]);
            $table->foreignId('buyer_id')->nullable(false)->change();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'search_criteria', 'status']);
        });
    }
};
