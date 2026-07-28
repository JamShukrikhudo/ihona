<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('contractor_id')->nullable()->after('property_id')->constrained()->nullOnDelete();
            $table->string('priority')->default('normal');
            $table->json('photos')->nullable();
            $table->json('quote_references')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->string('payment_status')->default('not_applicable');
            $table->timestamp('completed_at')->nullable();
            $table->index(['team_id', 'status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['contractor_id']);
            $table->dropIndex(['team_id', 'status', 'priority']);
            $table->dropColumn([
                'contractor_id', 'priority', 'photos', 'quote_references',
                'invoice_reference', 'payment_status', 'completed_at',
            ]);
        });
    }
};
