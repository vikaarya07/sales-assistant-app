<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case NEW = 'new';
    case BLAST = 'blast';
    case NO_RESPON = 'no_respon';
    case FOLLOW_UP = 'follow_up';
    case INTERESTED = 'interested';
    case NOT_INTERESTED = 'not_interested';
    case STNK_SOL = 'stnk_sol';
    case APP_IN = 'app_in';
    case VALID = 'valid';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baru',
            self::BLAST => 'Blast',
            self::NO_RESPON => 'Tidak Respon',
            self::FOLLOW_UP => 'Follow Up',
            self::INTERESTED => 'Minat',
            self::NOT_INTERESTED => 'Tidak Minat',
            self::STNK_SOL => 'STNK SOL',
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
            self::NO_RESPON => 'slate',
            self::FOLLOW_UP => 'amber',
            self::INTERESTED => 'lime',
            self::NOT_INTERESTED => 'pink',
            self::STNK_SOL => 'violet',
            self::APP_IN => 'teal',
            self::VALID => 'green',
            self::INVALID => 'red',
        };
    }
}
