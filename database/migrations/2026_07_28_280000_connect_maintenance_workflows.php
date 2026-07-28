<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('contractor_id')->constrained()->nullOnDelete();
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('maintenance_request_id')->nullable()->after('property_id')->constrained()->nullOnDelete();
        });

        Schema::table('vendor_quotes', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('maintenance_request_id')->nullable()->after('property_id')->constrained()->nullOnDelete();
            $table->index(['team_id', 'status', 'valid_until']);
        });

        DB::table('vendor_quotes')->orderBy('id')->each(function (object $quote): void {
            $teamId = DB::table('vendors')->where('id', $quote->vendor_id)->value('team_id');

            if ($teamId) {
                DB::table('vendor_quotes')->where('id', $quote->id)->update(['team_id' => $teamId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_quotes', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'status', 'valid_until']);
            $table->dropConstrainedForeignId('maintenance_request_id');
            $table->dropConstrainedForeignId('team_id');
        });
        Schema::table('work_orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('maintenance_request_id'));
        Schema::table('maintenance_requests', fn (Blueprint $table) => $table->dropConstrainedForeignId('vendor_id'));
    }
};
