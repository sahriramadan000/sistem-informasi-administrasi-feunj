<?php

namespace App\Enums;

enum SecurityClassification: string
{
    case BIASA = 'B';
    case TERBATAS = 'T';
    case RAHASIA = 'R';
    case SANGAT_RAHASIA = 'SR';

    public function label(): string
    {
        return match($this) {
            self::BIASA => 'Biasa',
            self::TERBATAS => 'Terbatas',
            self::RAHASIA => 'Rahasia',
            self::SANGAT_RAHASIA => 'Sangat Rahasia',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::BIASA => 'Dokumen dengan klasifikasi keamanan biasa',
            self::TERBATAS => 'Dokumen dengan akses terbatas',
            self::RAHASIA => 'Dokumen dengan tingkat keamanan rahasia',
            self::SANGAT_RAHASIA => 'Dokumen dengan tingkat keamanan paling tinggi',
        };
    }

    public static function options(): array
    {
        return [
            self::BIASA->value => self::BIASA->label(),
            self::TERBATAS->value => self::TERBATAS->label(),
            self::RAHASIA->value => self::RAHASIA->label(),
            self::SANGAT_RAHASIA->value => self::SANGAT_RAHASIA->label(),
        ];
    }

    public static function optionsWithDescription(): array
    {
        return [
            self::BIASA->value => self::BIASA->label() . ' - ' . self::BIASA->description(),
            self::TERBATAS->value => self::TERBATAS->label() . ' - ' . self::TERBATAS->description(),
            self::RAHASIA->value => self::RAHASIA->label() . ' - ' . self::RAHASIA->description(),
            self::SANGAT_RAHASIA->value => self::SANGAT_RAHASIA->label() . ' - ' . self::SANGAT_RAHASIA->description(),
        ];
    }
}
