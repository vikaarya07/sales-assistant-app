<?php

namespace App\Enums;

enum MusicGenre: string
{
    case POP = 'pop';
    case ROCK = 'rock';
    case HIP_HOP = 'hip-hop';
    case ELECTRONIC = 'electronic';
    case RNB = 'rnb';
    case JAZZ = 'jazz';
    case OLDIES = 'oldies';
    case REGGAE = 'reggae';
    case COUNTRY = 'country';
    case CLASSICAL = 'classical';
    case RELIGI = 'religi';
    case DANGDUT = 'dangdut';
    case INDONESIAN = 'indonesian';
    case BRAZILIAN = 'brazilian';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::POP => 'Pop',
            self::ROCK => 'Rock',
            self::HIP_HOP => 'Hip-Hop',
            self::ELECTRONIC => 'Electronic',
            self::RNB => 'R&B',
            self::JAZZ => 'Jazz',
            self::OLDIES => 'Oldies',
            self::REGGAE => 'Reggae',
            self::COUNTRY => 'Country',
            self::CLASSICAL => 'Classical',
            self::RELIGI => 'Religi',
            self::DANGDUT => 'Dangdut',
            self::INDONESIAN => 'Indonesian',
            self::BRAZILIAN => 'Brazilian',
            self::OTHER => 'Lainnya',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::POP => 'pink',
            self::ROCK => 'red',
            self::HIP_HOP => 'violet',
            self::ELECTRONIC => 'indigo',
            self::RNB => 'purple',
            self::JAZZ => 'amber',
            self::OLDIES => 'yellow',
            self::REGGAE => 'green',
            self::COUNTRY => 'orange',
            self::CLASSICAL => 'zinc',
            self::RELIGI => 'emerald',
            self::DANGDUT => 'lime',
            self::INDONESIAN => 'sky',
            self::BRAZILIAN => 'teal',
            self::OTHER => 'zinc',
        };
    }
}
