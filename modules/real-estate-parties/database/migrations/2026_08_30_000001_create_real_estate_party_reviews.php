<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_party_reviews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('party_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->string('moderation_status', 32)->default('pending')->index();
            $table->boolean('approved')->default(false)->index();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->unsignedInteger('unhelpful_votes')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['party_id', 'approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_party_reviews');
    }
};
