<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['team_id', 'name']);
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->string('job_title')->nullable()->after('department_id');
            $table->string('phone', 50)->nullable()->after('job_title');
            $table->text('bio')->nullable()->after('phone');
            $table->boolean('is_public')->default(true)->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['job_title', 'phone', 'bio', 'is_public']);
        });
        Schema::dropIfExists('departments');
    }
};
