<?php

namespace Database\Seeders;

use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Shield asks interactive questions when --option is omitted. That makes
        // db:seed hang (or return without permissions) in deploy/CI environments.
        // Generate only permissions here; policies are application code and do not
        // need to be regenerated every time the database is seeded.
        $panels = array_keys(Filament::getPanels());

        foreach ($panels as $panel) {
            $exitCode = Artisan::call('shield:generate', [
                '--all' => true,
                '--option' => 'permissions',
                '--panel' => $panel,
                '--no-interaction' => true,
            ]);

            if ($exitCode !== 0) {
                throw new RuntimeException(sprintf(
                    "Shield permission generation failed for panel '%s':\n%s",
                    $panel,
                    Artisan::output()
                ));
            }
        }
    }
}
