@php
$pickupDate  = optional($order->pickup_at)->format('d.m.Y');
$windowLabel = $order->pickup_window_label
    ?: trim(($order->pickup_at?->format('H:i') ?? '') . ($order->pickup_end_at ? '–'.$order->pickup_end_at->format('H:i') : '')) ?: '–';
$branch = $order->branch;
@endphp

<x-mail::message>
# Neue Bestellung

**Bestellnummer:** {{ $order->order_number }}

<x-mail::panel>

## Kundendaten

**Name**
{{ $order->customer_name }}

**E-Mail**
{{ $order->customer_email }}

**Telefon**
{{ $order->customer_phone }}

**Anschrift**
{{ $order->customer_adress }}
{{ $order->customer_city }}

**Notiz**
{{ $order->customer_note }}
</x-mail::panel>

## Filiale
<x-mail::panel>

**Name**
{{ $branch->name ?? '—' }}

**Adresse**
@isset($branch->street){{ $branch->street }}@endisset
@isset($branch->zip){{ $branch->zip }}@endisset @isset($branch->city){{ $branch->city }}@endisset
</x-mail::panel>

## Abholung
<x-mail::panel>

**Datum**
{{ $pickupDate }}

**Zeitfenster**
{{ $windowLabel }}

@isset($order->customer_note)
## Notiz des Kunden
{{ $order->customer_note }}
@endisset
</x-mail::panel>


<x-mail::button :url="\App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $order], panel: 'verwaltung')">
Im Backend öffnen
</x-mail::button>

</x-mail::message>
