<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->json('attendees')->nullable()->after('contact');
            $table->timestamp('confirmation_sent_at')->nullable()->after('notes');
            $table->timestamp('reminder_sent_at')->nullable()->after('confirmation_sent_at');
            $table->string('outcome')->nullable()->after('status');
            $table->text('outcome_notes')->nullable()->after('outcome');
        });

        Schema::table('viewing_feedbacks', function (Blueprint $table) {
            $table->index(['team_id', 'appointment_id']);
        });
    }

    public function down(): void
    {
        Schema::table('viewing_feedbacks', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'appointment_id']);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'attendees',
                'confirmation_sent_at',
                'reminder_sent_at',
                'outcome',
                'outcome_notes',
            ]);
        });
    }
};
