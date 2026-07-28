<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lease_agreements', function (Blueprint $table) {
            $table->string('deposit_scheme')->nullable();
            $table->string('deposit_reference')->nullable();
            $table->foreignId('renewal_of_id')->nullable()->constrained('lease_agreements')->nullOnDelete();
            $table->string('notice_type')->nullable();
            $table->date('notice_served_at')->nullable();
            $table->date('notice_expires_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('end_reason')->nullable();
            $table->index(['team_id', 'status', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::table('lease_agreements', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'status', 'end_date']);
            $table->dropForeign(['renewal_of_id']);
            $table->dropColumn([
                'deposit_scheme', 'deposit_reference', 'renewal_of_id', 'notice_type',
                'notice_served_at', 'notice_expires_at', 'ended_at', 'end_reason',
            ]);
        });
    }
};
