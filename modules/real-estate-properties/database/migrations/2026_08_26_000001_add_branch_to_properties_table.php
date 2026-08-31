<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('team_id')
                ->constrained('real_estate_branches')
                ->nullOnDelete();
            $table->index(['team_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_properties', function (Blueprint $table): void {
            $table->dropForeign(['branch_id']);
            $table->dropIndex('real_estate_properties_team_id_branch_id_index');
            $table->dropColumn('branch_id');
        });
    }
};
