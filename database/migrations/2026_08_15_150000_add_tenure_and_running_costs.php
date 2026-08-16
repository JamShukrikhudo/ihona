<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What a property costs to hold is the question asked immediately after the
 * price, and none of it could be shown because none of it was stored. The
 * disclosure panel already listed council tax as a fact it should carry.
 *
 * All nullable: a record that does not hold one of these must say so rather
 * than default to a figure nobody supplied.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // A to H in England and Scotland, A to I in Wales.
            $table->string('council_tax_band', 2)->nullable()->after('year_built');
            $table->string('tenure', 32)->nullable()->after('council_tax_band');
            $table->unsignedSmallInteger('lease_years_remaining')->nullable()->after('tenure');

            // Annual, and decimal rather than float: these are money.
            $table->decimal('service_charge', 10, 2)->nullable()->after('lease_years_remaining');
            $table->decimal('ground_rent', 10, 2)->nullable()->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'council_tax_band', 'tenure', 'lease_years_remaining',
                'service_charge', 'ground_rent',
            ]);
        });
    }
};
