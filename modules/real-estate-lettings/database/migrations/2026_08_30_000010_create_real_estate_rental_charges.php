<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_rental_charges', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('tenant_party_id')->nullable()->index();
            $table->unsignedBigInteger('lease_agreement_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->date('charge_date');
            $table->string('description');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'charge_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_rental_charges');
    }
};
