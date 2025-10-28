@php
$fmt = fn($v) => number_format((float)$v, 2, ',', '.') . ' €';
$pickupDate   = optional($order->pickup_at)->format('d.m.Y');
$windowLabel  = $order->pickup_window_label
    ?: trim(($order->pickup_at?->format('H:i') ?? '') . ($order->pickup_end_at ? '–'.$order->pickup_end_at->format('H:i') : '')) ?: '–';
$bestellDatum = optional($order->created_at)->format('d.m.Y');
$branch       = $order->branch;
@endphp
<x-mail::message>
# Bestellbestätigung

**Ihre Bestellung {{ $order->order_number }}**

Hallo {{ $order->customer_name }},
vielen Dank für Ihre Bestellung. Im Folgenden finden Sie alle Details zu Abholung und Bestellung.

## Ihre Bestelldetails
<x-mail::panel>
**Name**
{{ $order->customer_name }}

**Anschrift**
{{ $order->customer_adress }}
{{ $order->customer_city }}

**Bestellnummer**
{{ $order->order_number }}

**Zahlungsart**
Zahlung vor Ort

**Bestelldatum**
{{ $bestellDatum }}
</x-mail::panel>

## Abholung in Ihrer Filiale
<x-mail::panel>
**Filiale**
{{ $branch->name ?? '—' }}

**Adresse**
{{ $branch->street }}, {{ $branch->zip }} {{ $branch->city }}

**Abholdatum**
{{ $pickupDate }}

**Abholzeitfenster**
{{ $windowLabel }}

@isset($oh_text)
<small style="color:#6b7280">Öffnungszeiten am Abholtag: {{ $oh_text }}</small>
@endisset
</x-mail::panel>

## Ihre Bestellung
<x-mail::table>
| Produkt | Menge | Einzelpreis | Summe |
|:--|:--:|--:|--:|
@foreach($order->items as $item)
@php
$title = $item->product_name_snapshot . ($item->variant_name_snapshot ? ' — '.$item->variant_name_snapshot : '');
$options = $item->options->map(function($opt){
    $label = $opt->option_name_snapshot;
    if ($opt->value_label_snapshot) $label .= ': '.$opt->value_label_snapshot;
    if ($opt->free_text) $label .= ' ('.$opt->free_text.')';
    $delta = (float)$opt->price_delta_snapshot !== 0.0 ? ' ['.number_format((float)$opt->price_delta_snapshot,2,',','.').' €]' : '';
    return $label.$delta;
})->filter();
if ($options->isNotEmpty()) $title .= "<br><span style='color:#6b7280'>".$options->implode('<br>')."</span>";
@endphp
| {!! $title !!} | {{ $item->quantity }} | {{ $fmt($item->price_snapshot) }} | {{ $fmt($item->line_total) }} |
@endforeach
</x-mail::table>

{{-- Totals – Gesamtpreis hervorgehoben --}}
<x-mail::panel>
**Zwischensumme:** {{ $fmt($order->subtotal) }}
**MwSt.:** {{ $fmt($order->tax_total) }}

**Gesamtpreis:** **{{ $fmt($order->grand_total) }}**
</x-mail::panel>

@isset($order->customer_note)
## Hinweis zur Bestellung
{{ $order->customer_note }}
@endisset

Freundliche Grüße
{{ config('app.name') }}
</x-mail::message>
