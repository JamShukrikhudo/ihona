<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type');
            $table->decimal('previous_amount', 14, 2)->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('previous_status')->nullable();
            $table->string('status');
            $table->text('conditions')->nullable();
            $table->text('note')->nullable();
            $table->json('changes')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['team_id', 'offer_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_events');
    }
};
