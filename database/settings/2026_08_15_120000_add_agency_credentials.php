<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

/**
 * Adds the registration and redress fields the footer's title block was built
 * to display in ticket 03. Only these four keys are touched, so values already
 * recorded against the general group are left as they are.
 */
return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.company_registration_number', null);
        $this->migrator->add('general.ico_registration_number', null);
        $this->migrator->add('general.vat_number', null);
        $this->migrator->add('general.redress_scheme', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.company_registration_number');
        $this->migrator->delete('general.ico_registration_number');
        $this->migrator->delete('general.vat_number');
        $this->migrator->delete('general.redress_scheme');
    }
};
