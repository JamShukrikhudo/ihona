<?php

namespace Tests\Feature;

use App\Filament\Admin\Pages\ManageGeneralSettings;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 13 of the Survey Sheet rollout: agency registration and redress.
 *
 * A drawing sheet ends in a title block naming who drew it and under what
 * authority. On a UK property site those details are not decoration: an agent
 * must publish their redress scheme, and a limited company must publish its
 * registration number. The title block was built in ticket 03 with nothing to
 * read them from.
 */
class AgencyCredentialsTest extends TestCase
{
    use RefreshDatabase;

    private const CREDENTIALS = [
        'company_registration_number' => '09876543',
        'ico_registration_number' => 'ZA123456',
        'vat_number' => 'GB 123 4567 89',
        'redress_scheme' => 'The Property Ombudsman',
    ];

    private function settings(array $values = []): GeneralSettings
    {
        $settings = app(GeneralSettings::class);

        foreach ($values as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        return $settings;
    }

    public function test_an_administrator_can_record_them(): void
    {
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole(\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($admin);

        Livewire::test(ManageGeneralSettings::class)
            ->fillForm(self::CREDENTIALS)
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(GeneralSettings::class);

        foreach (self::CREDENTIALS as $key => $value) {
            $this->assertSame($value, $settings->{$key}, "[{$key}] did not survive the save");
        }
    }

    public function test_they_reach_the_footer_of_every_public_page(): void
    {
        $this->settings(self::CREDENTIALS);

        foreach (['/', '/properties', '/about', '/contact'] as $uri) {
            $page = $this->get($uri)->assertOk();

            foreach (self::CREDENTIALS as $key => $value) {
                $page->assertSee($value, false);
            }

            $page->assertSee('Redress scheme');
        }
    }

    /**
     * Every one of these is optional — a sole trader has no company number, a
     * business under the threshold has no VAT number — and an empty label in a
     * title block reads as a missing fact rather than one that does not apply.
     */
    public function test_a_credential_that_is_not_held_is_left_out_entirely(): void
    {
        $this->settings(array_merge(self::CREDENTIALS, [
            'vat_number' => null,
            'ico_registration_number' => '',
        ]));

        $page = $this->get('/')->assertOk();

        $page->assertDontSee('VAT number');
        $page->assertDontSee('ICO registration');
        $page->assertSee('Company number');
        $page->assertSee('Redress scheme');
    }

    public function test_the_office_details_are_still_there(): void
    {
        $this->settings(array_merge(self::CREDENTIALS, [
            'site_address' => '12 Broad Street, Reading RG1 2BH',
            'site_phone' => '0118 496 0000',
        ]));

        $this->get('/')
            ->assertOk()
            ->assertSee('12 Broad Street, Reading RG1 2BH')
            ->assertSee('0118 496 0000')
            ->assertSee('09876543');
    }

    /**
     * The settings migration adds keys; it must not reset the ones already
     * recorded, which is what a naive re-seed of the group would do.
     */
    public function test_the_migration_leaves_existing_settings_alone(): void
    {
        $this->settings(['site_name' => 'Whitmore & Co']);

        $this->artisan('migrate', ['--force' => true])->assertSuccessful();

        $this->assertSame('Whitmore & Co', app(GeneralSettings::class)->site_name);
    }

    public function test_every_credential_is_optional_on_the_settings_object(): void
    {
        $settings = app(GeneralSettings::class);

        foreach (array_keys(self::CREDENTIALS) as $key) {
            $property = new \ReflectionProperty($settings, $key);

            $this->assertTrue(
                $property->getType()?->allowsNull(),
                "[{$key}] has to be optional: not every agency holds one"
            );
        }
    }
}
