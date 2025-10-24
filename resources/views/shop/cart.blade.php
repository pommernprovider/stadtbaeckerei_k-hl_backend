{{-- resources/views/shop/cart.blade.php --}}
@extends('layouts.shop')
@section('title', 'Warenkorb')

@section('content')

    {{-- Page Header / Breadcrumb --}}
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h1 class="page-name">Warenkorb</h1>
                        <ol class="breadcrumb">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li class="active">Warenkorb</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($items->isEmpty())
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">Dein Warenkorb ist leer.</div>
                    <a href="{{ route('shop.products') }}" class="btn btn-main">Weiter einkaufen</a>
                </div>
            </div>
        </div>
    @else
        <div class="page-wrapper">
            <div class="cart shopping">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="product-list">

                                    {{-- Warenkorb-Tabelle --}}
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Artikel</th>
                                                <th class="text-center" style="width:160px;">Menge</th>
                                                <th class="text-right" style="width:140px;">Preis</th>
                                                <th class="text-right" style="width:160px;">Summe</th>
                                                <th class="text-right" style="width:120px;">Aktion</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($items as $row)
                                                @php
                                                    $product = $row['product'];
                                                    $img = $product?->getFirstMediaUrl('product_main', 'thumb')
                                                        ?: $product?->getFirstMediaUrl('product_main')
                                                        ?: 'https://via.placeholder.com/80x80';
                                                @endphp

                                                <tr>
                                                    {{-- Artikel / Bild / Optionen / Netto/MwSt-Zeile --}}
                                                    <td>
                                                        <div class="product-info"
                                                            style="display:flex; gap:12px; align-items:flex-start;">
                                                            <a href="{{ $product ? route('shop.product', $product) : '#' }}">
                                                                <img width="80" height="80" style="object-fit:cover;"
                                                                    src="{{ $img }}" alt="{{ $product?->name ?? 'Artikel' }}">
                                                            </a>
                                                            <div>
                                                                <a href="{{ $product ? route('shop.product', $product) : '#' }}">
                                                                    <strong>{{ $product?->name ?? '—' }}</strong>
                                                                </a>

                                                                {{-- Gewählte Optionen --}}
                                                                @if (!empty($row['options']))
                                                                    <div class="text-muted" style="font-size:12px; margin-top:6px;">
                                                                        @foreach($row['options'] as $opt)
                                                                            <div>
                                                                                <span
                                                                                    class="text-semibold">{{ $opt['option_name'] ?? 'Option' }}:</span>
                                                                                @if(!empty($opt['free_text']))
                                                                                    <span>{{ $opt['free_text'] }}</span>
                                                                                @else
                                                                                    <span>{{ $opt['value_label'] ?? '—' }}</span>
                                                                                @endif
                                                                                @if(isset($opt['price_delta']) && (float) $opt['price_delta'] !== 0.0)
                                                                                    <span>
                                                                                        ({{ number_format((float) $opt['price_delta'], 2, ',', '.') }}
                                                                                        €)</span>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Menge ändern --}}
                                                    <td class="text-center">
                                                        <form action="{{ route('cart.update') }}" method="post" class="form-inline"
                                                            style="display:inline-flex; gap:6px; align-items:center; justify-content:center;">
                                                            @csrf
                                                            <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                            <input type="number" name="qty" value="{{ $row['qty'] }}" min="1"
                                                                class="form-control input-sm" style="width:70px;">
                                                            <button class="btn btn-default btn-sm">OK</button>
                                                        </form>
                                                    </td>

                                                    {{-- Einzelpreis (Brutto) --}}
                                                    <td class="text-right">
                                                        {{ number_format($row['unit'], 2, ',', '.') }} €
                                                    </td>

                                                    {{-- Zeilensumme (Brutto) --}}
                                                    <td class="text-right">
                                                        <strong>{{ number_format($row['sum'], 2, ',', '.') }} €</strong>
                                                    </td>

                                                    {{-- Entfernen --}}
                                                    <td class="text-right">
                                                        <form action="{{ route('cart.remove') }}" method="post"
                                                            onsubmit="return confirm('Artikel entfernen?');"
                                                            style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                            <button class="product-remove btn btn-link" type="submit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                                    <path
                                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    {{-- Summen / MwSt --}}
                                    <div class="row" style="margin-top:15px;">
                                        <div class="col-sm-6">
                                            <div class="text-muted" style="margin-top:8px;">
                                                Alle Preise inkl. gesetzl. MwSt.
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="cart-summary" style="text-align:right;">
                                                <div>Zwischensumme (Netto):
                                                    <strong>{{ number_format($subtotal, 2, ',', '.') }} €</strong>
                                                </div>
                                                <div>MwSt gesamt:
                                                    <strong>{{ number_format($taxTotal, 2, ',', '.') }} €</strong>
                                                </div>

                                                @if(!empty($taxByRate))
                                                    <div class="text-muted" style="font-size:12px; margin-top:4px;">
                                                        @foreach($taxByRate as $rate => $amt)
                                                            <div>davon {{ number_format($rate, 2, ',', '.') }}%:
                                                                {{ number_format($amt, 2, ',', '.') }} €
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div style="font-size:18px; margin-top:6px;">
                                                    Gesamt (Brutto): <strong>{{ number_format($grand, 2, ',', '.') }} €</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="row" style="margin-top:15px;">
                                        <div class="col-sm-6">
                                            <a href="{{ route('home') }}" class="btn btn-default">Weiter
                                                einkaufen</a>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            <a href="{{ route('checkout.show') }}" class="btn btn-main">Zur Kasse</a>
                                        </div>
                                    </div>

                                </div> {{-- /.product-list --}}
                            </div> {{-- /.block --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection