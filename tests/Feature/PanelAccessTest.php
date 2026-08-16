<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Dashboard;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    /** Registering as a seller then landing on /seller returned 403: no role but admin could open its own panel. */
    public function test_each_role_can_open_the_panel_it_is_redirected_to(): void
    {
        foreach (['agent', 'buyer', 'seller', 'landlord', 'tenant', 'contractor', 'staff', 'admin'] as $role) {
            $this->assertTrue(
                $this->userWithRole($role)->canAccessPanel(filament()->getPanel($role)),
                "a $role cannot open the $role panel it is redirected to"
            );
        }
    }

    /** The dashboards still used <x-filament::grid>, dropped in Filament 5, so every one of them threw once you got in. */
    public function test_each_panel_dashboard_renders(): void
    {
        $dashboards = [
            'app' => Dashboard::class,
            'buyer' => \App\Filament\Buyer\Pages\Dashboard::class,
            'contractor' => \App\Filament\Contractor\Pages\Dashboard::class,
            'seller' => \App\Filament\Seller\Pages\Dashboard::class,
            'tenant' => \App\Filament\Tenant\Pages\Dashboard::class,
        ];

        foreach ($dashboards as $panel => $page) {
            filament()->setCurrentPanel($panel);
            $this->actingAs($this->userWithRole($panel === 'app' ? 'staff' : $panel));

            Livewire::test($page)->assertOk();
        }
    }

    public function test_a_role_cannot_open_another_roles_panel(): void
    {
        $this->assertFalse($this->userWithRole('tenant')->canAccessPanel(filament()->getPanel('landlord')));
    }

    public function test_staff_keeps_the_shared_app_panel_and_a_buyer_does_not(): void
    {
        $this->assertTrue($this->userWithRole('staff')->canAccessPanel(filament()->getPanel('app')));
        $this->assertFalse($this->userWithRole('buyer')->canAccessPanel(filament()->getPanel('app')));
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['current_team_id' => Team::factory()->create()->id]);
        $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));

        return $user;
    }
}
