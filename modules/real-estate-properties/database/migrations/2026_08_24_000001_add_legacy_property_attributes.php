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
            ]);
        });
    }
};
