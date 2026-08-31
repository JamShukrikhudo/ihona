<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Liberu\Foundation\Organizations\Models\Team;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleData = [
            'name' => 'super_admin',
            'guard_name' => 'web',
        ];

        // Roles are team-scoped (permission.teams=true). Create + query them
        // inside the default team's context. See CLAUDE.md tenancy rules.
        if (Utils::isTenancyEnabled()) {
            $team = Team::firstOrFail();
            $roleData['team_id'] = $team->id;
            setPermissionsTeamId($team->id);
        }

        $superAdminRole = Role::firstOrCreate($roleData);

        // Grant every generated web permission (none until shield:generate runs — harmless).
        $permissions = Permission::where('guard_name', 'web')->pluck('id')->toArray();
        $superAdminRole->syncPermissions($permissions);

        // 'admin' is the second role User::hasAdminAccess() checks (alongside
        // super_admin) for /admin panel access — for staff who need the admin
        // panel without the full super_admin bypass Gate::before grants.
        $adminRoleData = ['name' => 'admin', 'guard_name' => 'web'] + array_intersect_key($roleData, ['team_id' => true]);
        $adminRole = Role::firstOrCreate($adminRoleData);
        $adminRole->syncPermissions($permissions);
    }
}
