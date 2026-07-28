<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_valuations', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->nullable()->after('valuation_date');
            $table->foreignId('assigned_to')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
            $table->dateTime('follow_up_at')->nullable()->after('scheduled_at');
            $table->timestamp('completed_at')->nullable()->after('follow_up_at');
            $table->string('outcome')->nullable()->after('status');
            $table->text('outcome_notes')->nullable()->after('outcome');

            $table->index(['team_id', 'scheduled_at']);
            $table->index(['team_id', 'follow_up_at']);
        });
    }

    public function down(): void
    {
        Schema::table('property_valuations', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'scheduled_at']);
            $table->dropIndex(['team_id', 'follow_up_at']);
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn([
                'scheduled_at',
                'follow_up_at',
                'completed_at',
                'outcome',
                'outcome_notes',
            ]);
        });
    }
};
