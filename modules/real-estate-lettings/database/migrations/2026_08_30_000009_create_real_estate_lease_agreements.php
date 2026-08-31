<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_lease_agreements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('tenant_party_id')->nullable()->index();
            $table->unsignedBigInteger('landlord_party_id')->nullable()->index();
            $table->unsignedBigInteger('renewal_of_id')->nullable()->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('security_deposit', 12, 2)->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('payment_frequency')->nullable();
            $table->string('deposit_scheme')->nullable();
            $table->string('deposit_reference')->nullable();
            $table->text('terms')->nullable();
            $table->text('content')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->boolean('is_signed')->default(false);
            $table->boolean('landlord_signed')->default(false);
            $table->boolean('tenant_signed')->default(false);
            $table->string('smart_contract_address')->nullable();
            $table->string('contract_status')->default('pending');
            $table->timestamp('contract_deployed_at')->nullable();
            $table->text('agreement_hash')->nullable();
            $table->string('blockchain_network')->nullable();
            $table->string('notice_type')->nullable();
            $table->date('notice_served_at')->nullable();
            $table->date('notice_expires_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('end_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_lease_agreements');
    }
};
