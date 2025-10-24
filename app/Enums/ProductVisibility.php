<?php

namespace App\Enums;

enum ProductVisibility: string
{
    case Draft       = 'draft';
    case Active      = 'active';
    case Unavailable = 'unavailable';
    case Seasonal    = 'seasonal';

    public function label(): string
    {
        return match ($this) {
            self::Draft       => 'Entwurf',
            self::Active      => 'Aktiv',
            self::Unavailable => 'Nicht verfügbar',
            self::Seasonal    => 'Saisonal',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft       => 'secondary',
            self::Active      => 'success',
            self::Unavailable => 'danger',
            self::Seasonal    => 'warning',
        };
    }

    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $c) {
            $out[$c->value] = $c->label();
        }
        return $out;
    }
}
