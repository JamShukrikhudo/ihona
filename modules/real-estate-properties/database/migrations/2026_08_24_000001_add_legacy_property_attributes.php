<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->string('title')->nullable()->after('address');
            $table->text('description')->nullable()->after('title');
            $table->decimal('price', 15, 2)->nullable()->after('description');
            $table->string('currency', 3)->nullable()->after('price');
            $table->unsignedSmallInteger('bedrooms')->nullable()->after('currency');
            $table->unsignedSmallInteger('bathrooms')->nullable()->after('bedrooms');
            $table->decimal('area_sqft', 12, 2)->nullable()->after('bathrooms');
            $table->unsignedSmallInteger('year_built')->nullable()->after('area_sqft');
            $table->json('structured_address')->nullable()->after('address');
            $table->decimal('latitude', 10, 7)->nullable()->after('structured_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('postal_code', 20)->nullable()->after('longitude');
            $table->string('country', 2)->nullable()->after('postal_code');
            $table->string('tenure', 40)->nullable()->after('country');
            $table->unsignedSmallInteger('lease_years_remaining')->nullable()->after('tenure');
            $table->decimal('service_charge', 12, 2)->nullable()->after('lease_years_remaining');
            $table->decimal('ground_rent', 12, 2)->nullable()->after('service_charge');
            $table->string('energy_rating', 10)->nullable()->after('ground_rent');
            $table->json('epc')->nullable()->after('energy_rating');
            $table->string('virtual_tour_url')->nullable()->after('epc');
            $table->string('virtual_tour_provider', 40)->nullable()->after('virtual_tour_url');
            $table->string('model_3d_url')->nullable()->after('virtual_tour_provider');
            $table->json('floor_plan_data')->nullable()->after('model_3d_url');
            $table->string('rightmove_id')->nullable()->after('floor_plan_data');
            $table->string('zoopla_id')->nullable()->after('rightmove_id');
            $table->string('onthemarket_id')->nullable()->after('zoopla_id');
            $table->timestamp('last_synced_at')->nullable()->after('onthemarket_id');
            $table->unsignedSmallInteger('reception_rooms')->nullable()->after('bathrooms');
            $table->json('parking')->nullable()->after('reception_rooms');
            $table->json('gardens')->nullable()->after('parking');
            $table->string('council_tax_band', 10)->nullable()->after('energy_rating');
            $table->unsignedTinyInteger('energy_score')->nullable()->after('council_tax_band');
            $table->unsignedTinyInteger('walkability_score')->nullable()->after('energy_score');
            $table->text('walkability_description')->nullable()->after('walkability_score');
            $table->unsignedTinyInteger('transit_score')->nullable()->after('walkability_description');
            $table->text('transit_description')->nullable()->after('transit_score');
            $table->unsignedTinyInteger('bike_score')->nullable()->after('transit_description');
            $table->text('bike_description')->nullable()->after('bike_score');
            $table->timestamp('walkability_updated_at')->nullable()->after('bike_description');
            $table->date('list_date')->nullable()->after('published_at');
            $table->date('sold_date')->nullable()->after('list_date');
            $table->boolean('is_featured')->default(false)->after('sold_date');
            $table->boolean('live_tour_available')->default(false)->after('is_featured');
            $table->boolean('ar_tour_enabled')->default(false)->after('live_tour_available');
            $table->json('ar_tour_settings')->nullable()->after('ar_tour_enabled');
            $table->string('ar_placement_guide')->nullable()->after('ar_tour_settings');
            $table->decimal('ar_model_scale', 8, 4)->nullable()->after('ar_placement_guide');
            $table->string('holographic_tour_url')->nullable()->after('ar_model_scale');
            $table->string('holographic_provider')->nullable()->after('holographic_tour_url');
            $table->json('holographic_metadata')->nullable()->after('holographic_provider');
            $table->boolean('holographic_enabled')->default(false)->after('holographic_metadata');
            $table->date('energy_rating_date')->nullable()->after('holographic_enabled');
            $table->unsignedBigInteger('insurance_policy_id')->nullable()->after('energy_rating_date');
            $table->decimal('insurance_coverage_amount', 15, 2)->nullable()->after('insurance_policy_id');
            $table->decimal('insurance_premium', 12, 2)->nullable()->after('insurance_coverage_amount');
            $table->date('insurance_expiry_date')->nullable()->after('insurance_premium');
            $table->string('jupix_id')->nullable()->after('insurance_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropColumn([
                'title', 'description', 'price', 'currency', 'bedrooms', 'bathrooms', 'area_sqft',
                'year_built', 'structured_address', 'latitude', 'longitude', 'postal_code', 'country',
                'tenure', 'lease_years_remaining', 'service_charge', 'ground_rent', 'energy_rating',
                'epc', 'virtual_tour_url', 'virtual_tour_provider', 'model_3d_url', 'floor_plan_data',
                'rightmove_id', 'zoopla_id', 'onthemarket_id', 'last_synced_at',
                'reception_rooms', 'parking', 'gardens', 'council_tax_band', 'energy_score',
                'walkability_score', 'walkability_description', 'transit_score', 'transit_description',
                'bike_score', 'bike_description', 'walkability_updated_at', 'list_date', 'sold_date',
                'is_featured', 'live_tour_available', 'ar_tour_enabled', 'ar_tour_settings',
                'ar_placement_guide', 'ar_model_scale', 'holographic_tour_url', 'holographic_provider',
                'holographic_metadata', 'holographic_enabled', 'energy_rating_date', 'insurance_policy_id',
                'insurance_coverage_amount', 'insurance_premium', 'insurance_expiry_date', 'jupix_id',
            ]);
        });
    }
};
