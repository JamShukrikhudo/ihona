<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_work_orders', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('maintenance_request_id')->nullable()->index();
            $table->unsignedBigInteger('vendor_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->string('title');
            $table->text('description');
            $table->string('work_type');
            $table->unsignedTinyInteger('priority')->default(2);
            $table->string('status')->default('pending')->index();
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('started_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->decimal('materials_cost', 12, 2)->nullable();
            $table->decimal('labor_cost', 12, 2)->nullable();
            $table->boolean('emergency_job')->default(false);
            $table->boolean('requires_access')->default(false);
            $table->text('access_instructions')->nullable();
            $table->json('safety_requirements')->nullable();
            $table->text('completion_notes')->nullable();
            $table->unsignedTinyInteger('customer_satisfaction')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('payment_status')->default('not_applicable');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'scheduled_date']);
        });

        Schema::create('real_estate_work_order_updates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('work_order_id')->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->string('update_type');
            $table->string('status_change')->nullable();
            $table->text('description');
            $table->unsignedTinyInteger('progress_percentage')->nullable();
            $table->decimal('time_spent', 8, 2)->nullable();
            $table->json('materials_used')->nullable();
            $table->json('issues_encountered')->nullable();
            $table->text('next_steps')->nullable();
            $table->timestamp('update_date');
            $table->boolean('is_customer_visible')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_work_order_updates');
        Schema::dropIfExists('real_estate_work_orders');
    }
};
