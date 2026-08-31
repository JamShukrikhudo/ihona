<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->foreignId('branch_id')->nullable()->index();
            $table->foreignId('company_id')->nullable()->index();
            $table->string('type')->default('applicant');
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
        });
        Schema::create('real_estate_contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('interest')->nullable();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_contact_messages');
        Schema::dropIfExists('real_estate_contacts');
    }
};
