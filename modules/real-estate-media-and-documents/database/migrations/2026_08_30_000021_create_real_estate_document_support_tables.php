<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class() extends Migration {
    public function up(): void {
        Schema::create('real_estate_document_categories', fn (Blueprint $t) => [$t->id(), $t->string('team_id'), $t->string('name'), $t->text('description')->nullable(), $t->timestamps()]);
        Schema::create('real_estate_document_category_media', fn (Blueprint $t) => [$t->foreignId('document_category_id'), $t->foreignId('media_document_id'), $t->primary(['document_category_id', 'media_document_id'])]);
        Schema::create('real_estate_document_templates', fn (Blueprint $t) => [$t->id(), $t->string('team_id'), $t->string('name'), $t->string('type'), $t->longText('content'), $t->timestamps()]);
        Schema::create('real_estate_document_versions', fn (Blueprint $t) => [$t->id(), $t->foreignId('media_document_id'), $t->string('team_id'), $t->unsignedInteger('version'), $t->string('file_name'), $t->string('file_path'), $t->string('checksum'), $t->text('notes')->nullable(), $t->timestamps()]);
        Schema::create('real_estate_document_signatures', fn (Blueprint $t) => [$t->id(), $t->foreignId('media_document_id'), $t->string('team_id'), $t->string('user_id'), $t->text('signature_data'), $t->string('signature_hash'), $t->timestamp('verified_at')->nullable(), $t->string('ip_address')->nullable(), $t->string('user_agent')->nullable(), $t->timestamps()]);
    }
    public function down(): void { foreach (['real_estate_document_signatures','real_estate_document_versions','real_estate_document_templates','real_estate_document_category_media','real_estate_document_categories'] as $table) Schema::dropIfExists($table); }
};
