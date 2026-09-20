<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reseeds real_estate_property_categories to the three categories the
 * Tajikistan market actually uses (Вторичка/Новостройка/Земля). Safe:
 * confirmed via tinker that zero live properties reference
 * property_category_id (all null) and the only existing row was an
 * unused placeholder ("arenda-kv-dom").
 */
return new class() extends Migration
{
    private const CATEGORIES = [
        ['name' => 'Вторичка', 'slug' => 'vtorichka'],
        ['name' => 'Новостройка', 'slug' => 'novostroyka'],
        ['name' => 'Земля', 'slug' => 'zemlya'],
    ];

    public function up(): void
    {
        $teamIds = DB::table('teams')->pluck('id');

        foreach ($teamIds as $teamId) {
            foreach (self::CATEGORIES as $category) {
                DB::table('real_estate_property_categories')->updateOrInsert(
                    ['team_id' => $teamId, 'slug' => $category['slug']],
                    ['name' => $category['name'], 'updated_at' => now(), 'created_at' => now()],
                );
            }
        }

        DB::table('real_estate_property_categories')
            ->where('slug', 'arenda-kv-dom')
            ->delete();
    }

    public function down(): void
    {
        $slugs = array_column(self::CATEGORIES, 'slug');
        DB::table('real_estate_property_categories')->whereIn('slug', $slugs)->delete();
    }
};
