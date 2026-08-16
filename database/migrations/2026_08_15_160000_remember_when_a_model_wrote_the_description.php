<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The description field takes whatever it is given, and a model can write into
 * it — the submission form has a "generate" button wired to OpenAI. Once saved,
 * the sentences a model produced are indistinguishable from the ones an agent
 * walked round the house to write, which is exactly the distinction a buyer
 * reading the copy is entitled to.
 *
 * Nullable rather than a boolean: the date is the other half of the fact, and
 * matches how the rest of the record states a source.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('description_generated_at')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('description_generated_at');
        });
    }
};
