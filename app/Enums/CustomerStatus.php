<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case REPLIED = 'replied';
    case FOLLOW_UP = 'follow_up';
    case INTERESTED = 'interested';
    case NOT_INTERESTED = 'not_interested';
    case CONVERTED = 'converted';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baru',
            self::CONTACTED => 'Sudah Dihubungi',
            self::REPLIED => 'Membalas',
            self::FOLLOW_UP => 'Follow Up',
            self::INTERESTED => 'Tertarik',
            self::NOT_INTERESTED => 'Tidak Tertarik',
            self::CONVERTED => 'Berhasil',
            self::INVALID => 'Invalid',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'zinc',
            self::CONTACTED => 'blue',
            self::REPLIED => 'cyan',
            self::FOLLOW_UP => 'amber',
            self::INTERESTED => 'green',
            self::NOT_INTERESTED => 'red',
            self::CONVERTED => 'emerald',
            self::INVALID => 'red',
        };
    }
}
