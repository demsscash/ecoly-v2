<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SECRETARY = 'secretary';
    case TEACHER = 'teacher';

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => __('Admin'),
            self::SECRETARY => __('Secretary'),
            self::TEACHER => __('Teacher'),
        };
    }

    /**
     * Get all roles as array for select options.
     */
    public static function options(): array
    {
        return collect(self::cases())->map(fn($role) => [
            'id' => $role->value,
            'name' => $role->label(),
        ])->toArray();
    }
}
