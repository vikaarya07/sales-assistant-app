<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PRIORITAS_DANA = 'prioritas_dana';
    case LANDING_PAGE = 'landing_page';
    case MULTIGUNA = 'multiguna';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::PRIORITAS_DANA => 'Prioritas Dana',
            self::LANDING_PAGE => 'Landing Page',
            self::MULTIGUNA => 'Multiguna',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'pink',
            self::PRIORITAS_DANA => 'emerald',
            self::LANDING_PAGE => 'amber',
            self::MULTIGUNA => 'blue',
        };
    }
}
