<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_offers', function (Blueprint $table): void {
            $table->string('currency', 3)->default('GBP')->after('amount');
            $table->unsignedBigInteger('negotiator_id')->nullable()->index()->after('party_id');
            $table->string('mortgage_status')->nullable()->after('qualification');
            $table->text('chain_information')->nullable()->after('mortgage_status');
            $table->text('conditions')->nullable()->after('chain_information');
            $table->timestamp('offered_at')->nullable()->after('conditions');
            $table->timestamp('responded_at')->nullable()->after('offered_at');
        });
        Schema::create('real_estate_offer_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('offer_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('event_type', 64);
            $table->decimal('previous_amount', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('previous_status', 32)->nullable();
            $table->string('status', 32)->nullable();
            $table->text('note')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['team_id', 'offer_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_offer_events');
        Schema::table('real_estate_offers', function (Blueprint $table): void {
            $table->dropColumn(['currency', 'negotiator_id', 'mortgage_status', 'chain_information', 'conditions', 'offered_at', 'responded_at']);
        });
    }
};
