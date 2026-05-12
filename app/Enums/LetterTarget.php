<?php

namespace App\Enums;

enum LetterTarget: string
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';

    public function label(): string
    {
        return match($this) {
            self::INTERNAL => 'Internal',
            self::EXTERNAL => 'External',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::INTERNAL => 'Surat untuk keperluan internal organisasi',
            self::EXTERNAL => 'Surat untuk keperluan eksternal/di luar organisasi - akan menambahkan kode UN39.',
        };
    }

    /**
     * Get kode target untuk nomor surat
     * Internal: '' (kosong) | External: 'UN39.'
     */
    public function code(): string
    {
        return match($this) {
            self::INTERNAL => '',
            self::EXTERNAL => 'UN39.',
        };
    }

    public static function options(): array
    {
        return [
            self::INTERNAL->value => self::INTERNAL->label(),
            self::EXTERNAL->value => self::EXTERNAL->label(),
        ];
    }

    public static function optionsWithDescription(): array
    {
        return [
            self::INTERNAL->value => self::INTERNAL->label() . ' - ' . self::INTERNAL->description(),
            self::EXTERNAL->value => self::EXTERNAL->label() . ' - ' . self::EXTERNAL->description(),
        ];
    }
}
