<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Secretary = 'secretary';
    case Teacher = 'teacher';
    case Parent = 'parent';

    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrateur',
            self::Secretary => 'Secrétaire',
            self::Teacher => 'Professeur',
            self::Parent => 'Parent',
        };
    }
}
