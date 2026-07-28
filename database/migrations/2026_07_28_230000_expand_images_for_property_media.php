<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('type', 30)->default('image')->after('property_id');
            $table->string('title')->nullable()->after('type');
            $table->string('alt_text')->nullable()->after('title');
            $table->string('disk', 30)->default('local')->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            $table->unsignedInteger('sort_order')->default(0)->after('file_size');
            $table->boolean('is_primary')->default(false)->after('sort_order');
            $table->boolean('is_public')->default(true)->after('is_primary');
            $table->boolean('watermark')->default(false)->after('is_public');
            $table->json('metadata')->nullable()->after('watermark');

            $table->index(['team_id', 'property_id', 'type', 'sort_order'], 'property_media_lookup');
        });

        DB::table('images')->whereNull('team_id')->update([
            'team_id' => DB::raw('(SELECT team_id FROM properties WHERE properties.id = images.property_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('property_media_lookup');
            $table->dropColumn([
                'type',
                'title',
                'alt_text',
                'disk',
                'file_size',
                'sort_order',
                'is_primary',
                'is_public',
                'watermark',
                'metadata',
            ]);
        });
    }
};
