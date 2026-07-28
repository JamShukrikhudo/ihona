<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('name');
            $table->json('audience_filters')->nullable()->after('content');
            $table->unsignedInteger('recipients_count')->default(0)->after('status');
            $table->unsignedInteger('delivered_count')->default(0)->after('recipients_count');
            $table->unsignedInteger('opened_count')->default(0)->after('delivered_count');
            $table->unsignedInteger('clicked_count')->default(0)->after('opened_count');
            $table->foreignId('created_by')->nullable()->after('team_id')->constrained('users')->nullOnDelete();
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['team_id', 'status']);
            $table->dropColumn([
                'subject', 'audience_filters', 'recipients_count', 'delivered_count',
                'opened_count', 'clicked_count', 'created_by',
            ]);
        });
    }
};
