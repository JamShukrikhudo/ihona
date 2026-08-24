<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('real_estate_valuations', function (Blueprint $table): void {
            $table->string('currency', 3)->nullable()->after('fee_amount');
            $table->timestamp('follow_up_at')->nullable()->after('completed_at');
            $table->json('conversion')->nullable()->after('recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('real_estate_valuations', function (Blueprint $table): void {
            $table->dropColumn(['currency', 'follow_up_at', 'conversion']);
        });
    }
};
