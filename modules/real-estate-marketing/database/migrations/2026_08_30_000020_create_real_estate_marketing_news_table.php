<?php
declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class() extends Migration {
    public function up(): void { Schema::create('real_estate_marketing_news', function (Blueprint $table): void { $table->id(); $table->string('team_id')->nullable()->index(); $table->string('title'); $table->string('slug')->unique(); $table->longText('content'); $table->string('excerpt')->nullable(); $table->string('featured_image')->nullable(); $table->boolean('is_featured')->default(false); $table->timestamp('published_at')->nullable()->index(); $table->timestamps(); $table->softDeletes(); }); }
    public function down(): void { Schema::dropIfExists('real_estate_marketing_news'); }
};
