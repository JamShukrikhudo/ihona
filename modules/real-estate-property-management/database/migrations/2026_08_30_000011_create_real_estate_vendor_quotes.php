<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_vendor_quotes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('maintenance_request_id')->nullable()->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->text('work_description');
            $table->decimal('quote_amount', 12, 2);
            $table->decimal('labor_cost', 12, 2)->nullable();
            $table->decimal('materials_cost', 12, 2)->nullable();
            $table->decimal('additional_costs', 12, 2)->nullable();
            $table->date('quote_date');
            $table->date('valid_until');
            $table->unsignedInteger('estimated_duration')->nullable();
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_vendor_quotes');
    }
};
