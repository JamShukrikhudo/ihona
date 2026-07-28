<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('version');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size');
            $table->string('checksum', 64);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['document_id', 'version']);
            $table->index(['team_id', 'document_id']);
        });

        Schema::table('digital_signatures', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_version_id')->nullable()->after('document_id')->constrained('document_versions')->nullOnDelete();
            $table->timestamp('signed_at')->nullable()->after('signature_data');
            $table->string('ip_address', 45)->nullable()->after('signed_at');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->index(['team_id', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::table('digital_signatures', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['document_version_id']);
            $table->dropIndex(['team_id', 'document_id']);
            $table->dropColumn(['team_id', 'document_version_id', 'signed_at', 'ip_address', 'user_agent']);
        });
        Schema::dropIfExists('document_versions');
    }
};
