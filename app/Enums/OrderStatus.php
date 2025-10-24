<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;

enum OrderStatus: string implements HasColor
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Ready     = 'ready';
    case PickedUp  = 'picked_up';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Offen',
            self::Confirmed => 'Bestätigt',
            self::Ready     => 'Bereit',
            self::PickedUp  => 'Abgeholt',
            self::Cancelled => 'Storniert',
        };
    }

    public function getColor(): string
    {

        return match ($this) {
            self::Pending   => 'secondary',
            self::Confirmed => 'info',
            self::Ready     => 'warning',
            self::PickedUp  => 'success',
            self::Cancelled => 'danger',
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
