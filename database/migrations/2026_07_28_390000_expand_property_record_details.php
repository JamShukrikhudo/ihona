<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->json('structured_address')->nullable()->after('location');
            $table->unsignedSmallInteger('reception_rooms')->default(0)->after('bathrooms');
            $table->json('parking')->nullable()->after('reception_rooms');
            $table->json('gardens')->nullable()->after('parking');
            $table->json('epc')->nullable()->after('energy_rating_date');
            $table->text('internal_notes')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'structured_address', 'reception_rooms', 'parking', 'gardens',
                'epc', 'internal_notes',
            ]);
        });
    }
};
