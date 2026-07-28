<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('address')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'name']);
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->json('emails')->nullable();
            $table->json('phones')->nullable();
            $table->json('addresses')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->string('preferred_language', 10)->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'type', 'status']);
            $table->index(['team_id', 'last_name', 'first_name']);
        });

        Schema::create('agency_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->nullableMorphs('taskable');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('open');
            $table->dateTime('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('checklist')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'status', 'due_at']);
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('negotiator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->char('currency', 3)->default('GBP');
            $table->string('status')->default('pending');
            $table->string('mortgage_status')->nullable();
            $table->text('chain_information')->nullable();
            $table->text('conditions')->nullable();
            $table->timestamp('offered_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
        Schema::dropIfExists('agency_tasks');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('companies');
    }
};
