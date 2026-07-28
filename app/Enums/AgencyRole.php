<?php

namespace App\Enums;

enum AgencyRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Manager = 'manager';
    case Editor = 'editor';
    case Member = 'member';

    public static function assignable(): array
    {
        return [
            self::Admin->value,
            self::Manager->value,
            self::Editor->value,
            self::Member->value,
        ];
    }

    public function defaultActions(): array
    {
        return match ($this) {
            self::Owner, self::Admin => ['*'],
            self::Manager => ['view', 'create', 'edit', 'export', 'approve', 'publish', 'archive'],
            self::Editor => ['view', 'create', 'edit'],
            self::Member => ['view'],
        };
    }

    public function canManage(self $target): bool
    {
        return match ($this) {
            self::Owner => $target !== self::Owner,
            self::Admin => in_array($target, [self::Manager, self::Editor, self::Member], true),
            default => false,
        };
    }

    public function canManageMemberships(): bool
    {
        return in_array($this, [self::Owner, self::Admin], true);
    }

    public function canEditStaffProfiles(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Manager], true);
    }
}
