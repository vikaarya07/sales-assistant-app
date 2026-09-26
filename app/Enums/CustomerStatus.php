<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case NEW = 'new';
    case BLAST = 'blast';
    case REPLIED = 'replied';
    case FOLLOW_UP = 'follow_up';
    case INTERESTED = 'interested';
    case NOT_INTERESTED = 'not_interested';
    case APP_IN = 'app_in';
    case VALID = 'valid';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baru',
            self::BLAST => 'Blast',
            self::REPLIED => 'Membalas',
            self::FOLLOW_UP => 'Follow Up',
            self::INTERESTED => 'Minat',
            self::NOT_INTERESTED => 'Tidak Minat',
            self::APP_IN => 'Pangajuan',
            self::VALID => 'Valid',
            self::INVALID => 'Gagal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'zinc',
            self::BLAST => 'blue',
            self::REPLIED => 'cyan',
            self::FOLLOW_UP => 'amber',
            self::INTERESTED => 'lime',
            self::NOT_INTERESTED => 'pink',
            self::APP_IN => 'teal',
            self::VALID => 'green',
            self::INVALID => 'red',
        };
    }
}
