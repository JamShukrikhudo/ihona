<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Liberu\Foundation\Organizations\Models\Team;
use Spatie\Permission\Models\Role;

class RealEstateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles are team-scoped (permission.teams=true). Create + query them
        // inside the default team's context. See CLAUDE.md tenancy rules.
        $team = null;

        if (Utils::isTenancyEnabled()) {
            $team = Team::firstOrFail();
            setPermissionsTeamId($team->id);
        }

        // TODO: assign a scoped permission subset to each role once
        // shield:generate has produced real-estate resource permissions
        // (Territory/Property/Viewing/Offer/Party) — do not guess a
        // permission list here.
        foreach (['host', 'sales_agent'] as $roleName) {
            Role::firstOrCreate(array_filter([
                'name' => $roleName,
                'guard_name' => 'web',
                'team_id' => $team?->id,
            ]));
        }
    }
}
