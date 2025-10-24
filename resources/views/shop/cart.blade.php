{{-- resources/views/shop/cart.blade.php --}}
@extends('layouts.shop')
@section('title', 'Warenkorb')

@section('content')

    {{-- Header / Breadcrumb --}}
    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Warenkorb</h1>
                    <nav aria-label="Breadcrumb" class="mt-2 text-sm">
                        <ol class="flex items-center gap-2 text-gray-600">
                            <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                            <li aria-hidden="true">/</li>
                            <li class="text-gray-900 font-medium">Warenkorb</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    @if($items->isEmpty())
        <section class="bg-white">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
                <div class="rounded-lg border border-blue-100 bg-blue-50 px-5 py-6">
                    <div class="flex items-start gap-3">
                        <svg class="h-6 w-6 text-blue-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 3h2l.4 2M7 13h10l3-7H6.4M7 13L5.4 5M7 13l-2 8h12m0 0a2 2 0 100-4 2 2 0 000 4zM9 21a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <div>
                            <p class="text-gray-800">Dein Warenkorb ist leer.</p>
                            <div class="mt-4">
                                <a href="{{ route('shop.products') }}"
                                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:opacity-90">
                                    Weiter einkaufen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="bg-white">
            <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-10">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {{-- LEFT: Positionen --}}
                    <div class="lg:col-span-8">
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            {{-- Table (ab md) --}}
                            <div class="hidden md:block">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3 font-medium">Artikel</th>
                                            <th class="px-4 py-3 text-center w-40 font-medium">Menge</th>
                                            <th class="px-4 py-3 text-right w-36 font-medium">Preis</th>
                                            <th class="px-4 py-3 text-right w-40 font-medium">Summe</th>
                                            <th class="px-4 py-3 text-right w-28 font-medium">Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($items as $row)
                                            @php
                                                /** @var \App\Models\Shop\Product|null $product */
                                                $product = $row['product'];
                                                $img = $product?->getFirstMediaUrl('product_main', 'thumb')
                                                    ?: $product?->getFirstMediaUrl('product_main')
                                                    ?: 'https://via.placeholder.com/80x80';
                                              @endphp
                                            <tr class="align-top">
                                                {{-- Artikel --}}
                                                <td class="px-4 py-4">
                                                    <div class="flex gap-3">
                                                        <a href="{{ $product ? route('shop.product', $product) : '#' }}"
                                                            class="block h-20 w-20 shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                            <img src="{{ $img }}" alt="{{ $product?->name ?? 'Artikel' }}"
                                                                class="h-full w-full object-cover">
                                                        </a>
                                                        <div>
                                                            <a href="{{ $product ? route('shop.product', $product) : '#' }}"
                                                                class="font-medium text-gray-900 hover:underline">
                                                                {{ $product?->name ?? '—' }}
                                                            </a>

                                                            {{-- Optionen --}}
                                                            @if (!empty($row['options']))
                                                                <div class="mt-2 text-xs text-gray-600 space-y-0.5">
                                                                    @foreach($row['options'] as $opt)
                                                                        <div>
                                                                            <span
                                                                                class="font-medium">{{ $opt['option_name'] ?? 'Option' }}:</span>
                                                                            @if(!empty($opt['free_text']))
                                                                                <span>{{ $opt['free_text'] }}</span>
                                                                            @else
                                                                                <span>{{ $opt['value_label'] ?? '—' }}</span>
                                                                            @endif
                                                                            @if(isset($opt['price_delta']) && (float) $opt['price_delta'] !== 0.0)
                                                                                <span>
                                                                                    ({{ number_format((float) $opt['price_delta'], 2, ',', '.') }}
                                                                                    €)
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Menge --}}
                                                <td class="px-4 py-4">
                                                    <form action="{{ route('cart.update') }}" method="post"
                                                        class="mx-auto flex w-full max-w-[9.5rem] items-center justify-center gap-2">
                                                        @csrf
                                                        <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                        <input type="number" name="qty" min="1" value="{{ $row['qty'] }}"
                                                            class="w-20 rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-900 focus:ring-gray-900">
                                                        <button
                                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                                            OK
                                                        </button>
                                                    </form>
                                                </td>

                                                {{-- Preis (brutto) --}}
                                                <td class="px-4 py-4 text-right text-gray-800">
                                                    {{ number_format($row['unit'], 2, ',', '.') }} €
                                                </td>

                                                {{-- Summe (brutto) --}}
                                                <td class="px-4 py-4 text-right">
                                                    <strong class="text-gray-900">{{ number_format($row['sum'], 2, ',', '.') }}
                                                        €</strong>
                                                </td>

                                                {{-- Entfernen --}}
                                                <td class="px-4 py-4 text-right">
                                                    <form action="{{ route('cart.remove') }}" method="post" class="inline"
                                                        onsubmit="return confirm('Artikel entfernen?');">
                                                        @csrf
                                                        <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-red-600 hover:bg-red-50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="currentColor" viewBox="0 0 16 16">
                                                                <path
                                                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                                <path
                                                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                            </svg>
                                                            Entfernen
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Mobile Cards (bis md) --}}
                            <div class="md:hidden divide-y divide-gray-100">
                                @foreach($items as $row)
                                    @php
                                        $product = $row['product'];
                                        $img = $product?->getFirstMediaUrl('product_main', 'thumb')
                                            ?: $product?->getFirstMediaUrl('product_main')
                                            ?: 'https://via.placeholder.com/120x120';
                                      @endphp
                                    <div class="p-4">
                                        <div class="flex gap-4">
                                            <a href="{{ $product ? route('shop.product', $product) : '#' }}"
                                                class="block h-24 w-24 shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                <img src="{{ $img }}" alt="{{ $product?->name ?? 'Artikel' }}"
                                                    class="h-full w-full object-cover">
                                            </a>
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ $product ? route('shop.product', $product) : '#' }}"
                                                    class="line-clamp-2 font-medium text-gray-900 hover:underline">
                                                    {{ $product?->name ?? '—' }}
                                                </a>

                                                {{-- Optionen --}}
                                                @if (!empty($row['options']))
                                                    <div class="mt-1 text-xs text-gray-600 space-y-0.5">
                                                        @foreach($row['options'] as $opt)
                                                            <div>
                                                                <span class="font-medium">{{ $opt['option_name'] ?? 'Option' }}:</span>
                                                                @if(!empty($opt['free_text']))
                                                                    <span>{{ $opt['free_text'] }}</span>
                                                                @else
                                                                    <span>{{ $opt['value_label'] ?? '—' }}</span>
                                                                @endif
                                                                @if(isset($opt['price_delta']) && (float) $opt['price_delta'] !== 0.0)
                                                                    <span>({{ number_format((float) $opt['price_delta'], 2, ',', '.') }}
                                                                        €)</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="mt-2 text-sm text-gray-800">
                                                    Einzelpreis: {{ number_format($row['unit'], 2, ',', '.') }} €
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 flex items-center justify-between">
                                            <form action="{{ route('cart.update') }}" method="post" class="flex items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                <input type="number" name="qty" min="1" value="{{ $row['qty'] }}"
                                                    class="w-20 rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-gray-900 focus:ring-gray-900">
                                                <button
                                                    class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                                                    OK
                                                </button>
                                            </form>

                                            <div class="text-right">
                                                <div class="text-sm text-gray-700">Summe</div>
                                                <div class="text-base font-semibold text-gray-900">
                                                    {{ number_format($row['sum'], 2, ',', '.') }} €
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <form action="{{ route('cart.remove') }}" method="post" class="inline"
                                                onsubmit="return confirm('Artikel entfernen?');">
                                                @csrf
                                                <input type="hidden" name="line" value="{{ $row['line'] }}">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-red-600 hover:bg-red-50">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                        <path
                                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                    </svg>
                                                    Entfernen
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Hinweis MwSt --}}
                        <p class="mt-3 text-sm text-gray-600">
                            Alle Preise inkl. gesetzl. MwSt.
                        </p>
                    </div>

                    {{-- RIGHT: Summary --}}
                    <div class="lg:col-span-4">
                        <div class="rounded-xl border border-gray-200 bg-white p-5">
                            <h2 class="text-lg font-semibold text-gray-900">Bestellübersicht</h2>

                            <dl class="mt-4 space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Zwischensumme (Netto)</dt>
                                    <dd class="font-medium text-gray-900">{{ number_format($subtotal, 2, ',', '.') }} €</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">MwSt gesamt</dt>
                                    <dd class="font-medium text-gray-900">{{ number_format($taxTotal, 2, ',', '.') }} €</dd>
                                </div>

                                @if(!empty($taxByRate))
                                    <div class="pt-1 text-xs text-gray-500">
                                        @foreach($taxByRate as $rate => $amt)
                                            <div class="flex justify-between">
                                                <span>davon {{ number_format($rate, 2, ',', '.') }}%</span>
                                                <span>{{ number_format($amt, 2, ',', '.') }} €</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </dl>

                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between text-base">
                                    <span class="font-medium text-gray-900">Gesamt (Brutto)</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($grand, 2, ',', '.') }} €</span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-2">
                                <a href="{{ route('checkout.show') }}"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:opacity-90">
                                    Zur Kasse
                                </a>
                                <a href="{{ route('home') }}"
                                    class="inline-flex w-full items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-50">
                                    Weiter einkaufen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    @endif

@endsection