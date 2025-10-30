@php
  $currency   = $order->currency ?? 'EUR';
  $symbol     = $currency === 'EUR' ? '€' : $currency;
  $fmt        = fn($v) => number_format((float)$v, 2, ',', '.') . ' ' . $symbol;

  $bestellDatum = optional($order->created_at)->format('d.m.Y');
  $pickupDate   = optional($order->pickup_at)->format('d.m.Y');
  $windowLabel  = $order->pickup_window_label
      ?: trim(($order->pickup_at?->format('H:i') ?? '') . ($order->pickup_end_at ? '–' . $order->pickup_end_at->format('H:i') : '')) ?: '–';

  $branch = $order->branch;

  // Manche Felder heißen bei dir "customer_tax" (Migration) – häufig ist das die PLZ.
  $zip = $order->customer_zip ?? $order->customer_tax ?? '';
@endphp

<x-mail::message>
# Neue Bestellung

**Bestellnummer:** {{ $order->order_number }}

## Bestellübersicht
<x-mail::panel>
**Bestelldatum**
{{ $bestellDatum }}

</x-mail::panel>

## Abholung
<x-mail::panel>
**Filiale**
{{ $branch->number }} - {{ $branch->name ?? '—' }}

**Adresse**
{{ $branch->street ?? '—' }}
{{ $branch->zip ?? '—' }} {{ $branch->city ?? '' }}

**Abholdatum**
{{ $pickupDate }}

**Zeitfenster**
{{ $windowLabel }}
</x-mail::panel>

## Kunde
<x-mail::panel>
**Name**
{{ $order->customer_name }}

**E-Mail**
{{ $order->customer_email }}

**Telefon**
{{ $order->customer_phone }}

**Anschrift**
{{ $order->customer_adress }}
{{ $zip }} {{ $order->customer_city }}

@isset($order->customer_note)
**Kundennotiz**
{{ $order->customer_note }}
@endisset
</x-mail::panel>

## Artikel
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
  if ($options->isNotEmpty()) {
      $title .= "<br><span style='color:#6b7280'>".$options->implode('<br>')."</span>";
  }
@endphp
| {!! $title !!} | {{ (int)$item->quantity }} | {{ $fmt($item->price_snapshot) }} | {{ $fmt($item->line_total) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
**Zwischensumme:** {{ $fmt($order->subtotal) }}
**MwSt.:** {{ $fmt($order->tax_total) }}

**Gesamt (Brutto):** **{{ $fmt($order->grand_total) }}**
</x-mail::panel>

<x-mail::button :url="\App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $order], panel: 'verwaltung')">
Im Backend öffnen
</x-mail::button>

</x-mail::message>
