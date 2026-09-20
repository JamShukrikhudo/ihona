<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drops UK/US real-estate-template columns not needed for the Tajikistan
 * market: holographic tours, EPC/energy-rating-date/service-charge/ground-
 * rent (UK leasehold concepts), insurance expiry tracking, walkability
 * staleness tracking, and the disabled Rightmove/Zoopla/OnTheMarket sync
 * ids (those modules are now default_enabled: false).
 */
return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropColumn([
                'holographic_tour_url', 'holographic_provider', 'holographic_metadata', 'holographic_enabled',
                'epc', 'energy_rating_date', 'service_charge', 'ground_rent', 'insurance_expiry_date',
                'walkability_updated_at', 'rightmove_id', 'zoopla_id', 'onthemarket_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->string('holographic_tour_url')->nullable();
            $table->string('holographic_provider')->nullable();
            $table->json('holographic_metadata')->nullable();
            $table->boolean('holographic_enabled')->default(false);
            $table->json('epc')->nullable();
            $table->date('energy_rating_date')->nullable();
            $table->decimal('service_charge', 12, 2)->nullable();
            $table->decimal('ground_rent', 12, 2)->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->timestamp('walkability_updated_at')->nullable();
            $table->string('rightmove_id')->nullable();
            $table->string('zoopla_id')->nullable();
            $table->string('onthemarket_id')->nullable();
        });
    }
};
