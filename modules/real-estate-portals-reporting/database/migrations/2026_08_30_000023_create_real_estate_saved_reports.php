<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('real_estate_saved_reports', fn (Blueprint $t) => [$t->id(), $t->string('team_id'), $t->string('created_by'), $t->string('name'), $t->string('type'), $t->json('filters')->nullable(), $t->boolean('is_shared')->default(false), $t->timestamps(), $t->softDeletes()]);
        Schema::create('real_estate_dashboard_layouts', fn (Blueprint $t) => [$t->id(), $t->string('team_id'), $t->string('user_id'), $t->string('name'), $t->json('widgets')->nullable(), $t->timestamps()]);
    }

    public function down(): void
    {
        Schema::dropIfExists('real_estate_dashboard_layouts');
        Schema::dropIfExists('real_estate_saved_reports');
    }
};
