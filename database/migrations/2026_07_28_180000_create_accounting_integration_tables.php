<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('name');
            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->string('last_sync_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['team_id', 'provider', 'name']);
        });

        Schema::create('accounting_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accounting_integration_id')->constrained()->cascadeOnDelete();
            $table->string('link_type', 64);
            $table->nullableMorphs('linkable');
            $table->string('external_id')->nullable();
            $table->string('invoice_reference', 191)->nullable();
            $table->string('payment_status')->default('unknown');
            $table->decimal('amount', 14, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'link_type', 'payment_status']);
            $table->unique(
                ['accounting_integration_id', 'linkable_type', 'linkable_id', 'link_type', 'invoice_reference'],
                'accounting_link_unique'
            );
        });

        Schema::create('accounting_sync_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accounting_integration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->unsignedInteger('processed')->default(0);
            $table->unsignedInteger('succeeded')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->json('errors')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_sync_runs');
        Schema::dropIfExists('accounting_links');
        Schema::dropIfExists('accounting_integrations');
    }
};
