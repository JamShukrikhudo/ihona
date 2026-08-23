<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_portal_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('team_id');
            $table->string('created_by')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('portal');
            $table->string('report_type');
            $table->string('status')->default('draft');
            $table->json('payload')->nullable();
            $table->json('metrics')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['team_id', 'portal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_portal_reports');
    }
};
