<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compliance_documents', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->softDeletes();
            $table->index(['team_id', 'expiry_date', 'is_verified']);
        });

        DB::table('compliance_documents')->orderBy('id')->each(function (object $document): void {
            $teamId = DB::table('compliance_items')
                ->where('id', $document->compliance_item_id)
                ->value('team_id');

            if ($teamId) {
                DB::table('compliance_documents')
                    ->where('id', $document->id)
                    ->update(['team_id' => $teamId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('compliance_documents', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'expiry_date', 'is_verified']);
            $table->dropConstrainedForeignId('team_id');
            $table->dropSoftDeletes();
        });
    }
};
