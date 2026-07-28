<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->char('currency', 3)->default('GBP')->after('price');
            $table->index(['team_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['team_id', 'branch_id', 'status']);
            $table->dropColumn('currency');
        });
    }
};
