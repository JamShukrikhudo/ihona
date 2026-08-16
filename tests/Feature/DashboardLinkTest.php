<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleBasedRedirect;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * The navbar built its dashboard link as `/{role}`, so a super_admin — whose
 * panel is `/admin` — got a "Super_admin dashboard" link to a route that does
 * not exist. Five copies of the role-to-panel map existed and two of them were
 * missing super_admin entirely; RoleBasedRedirect::PANELS is now the only one.
 */
class DashboardLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_super_admin_is_linked_to_the_admin_panel(): void
    {
        Role::create(['name' => 'super_admin']);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user);

        $html = view('components.home-navbar')->render();

        $this->assertStringContainsString('href="/admin"', $html);
        $this->assertStringContainsString('Admin dashboard', $html);
        $this->assertStringNotContainsString('super_admin', $html);
    }

    public function test_every_panel_in_the_map_is_a_real_filament_panel(): void
    {
        $paths = array_map(fn ($panel) => $panel->getPath(), Filament::getPanels());

        foreach (RoleBasedRedirect::PANELS as $role => $panel) {
            $this->assertContains($panel, $paths, "role {$role} points at /{$panel}, which no panel serves");
        }
    }
}
