@extends('layouts.shop')

@section('title', $category->name)

@section('content')

    {{-- Header / Breadcrumb --}}
    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">{{ $category->name }}</h1>
                <nav aria-label="Breadcrumb" class="hidden sm:block">
                    <ol class="flex items-center gap-2 text-sm text-gray-600">
                        <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-900 font-medium">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    {{-- Produkte --}}
    <section class="bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-10">
            @forelse($products as $p)
                @php
                    $img = $p->getFirstMediaUrl('product_main', 'web')
                        ?: $p->getFirstMediaUrl('product_main')
                        ?: 'https://via.placeholder.com/600x400';

                    $badge = $p->availabilityBadge();
                    $ribbonText = $badge ? $badge['text'] : ($p->visibility_status === 'seasonal' ? 'Saisonal' : null);
                  @endphp
            @empty
                <div class="rounded-xl border border-gray-200 p-8 text-center text-gray-600">
                    Keine Produkte in dieser Kategorie.
                </div>
            @endforelse

            @if($products->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $p)
                        @php
                            $img = $p->getFirstMediaUrl('product_main', 'web')
                                ?: $p->getFirstMediaUrl('product_main')
                                ?: 'https://via.placeholder.com/600x400';

                            $badge = $p->availabilityBadge();
                            $ribbonText = $badge ? $badge['text'] : ($p->visibility_status === 'seasonal' ? 'Saisonal' : null);
                            $tags = $p->relationLoaded('tags') ? $p->tags : $p->tags()->limit(3)->get();
                          @endphp

                        <div x-data="{ open:false }"
                            class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow transition">
                            <div class="relative">
                                {{-- Bild --}}
                                <a href="{{ route('shop.product', $p) }}" class="block">
                                    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                        <img src="{{ $img }}" alt="{{ $p->name }}"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                            loading="lazy" decoding="async">
                                    </div>
                                </a>

                                {{-- RIBBON: nutzt dein CSS .ribbon > span --}}
                                @if($ribbonText)
                                    <div class="ribbon">
                                        <span>{{ $ribbonText }}</span>
                                    </div>
                                @endif

                                {{-- TAG-BADGES: nutzt deine .tag-badges / .tag-badge Klassen --}}
                                @if($tags->isNotEmpty())
                                    <div class="tag-badges">
                                        @foreach($tags as $tag)
                                            <span class="tag-badge" title="{{ $tag->name }}">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Overlay-Actions (Quick View + Link) mit Lucide-Icons --}}
                                <div
                                    class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition group-hover:opacity-100">
                                    <div class="pointer-events-auto flex gap-2 rounded-full bg-white/90 p-2 shadow">
                                        <button @click="open=true" class="rounded-full p-2 hover:bg-gray-100"
                                            aria-label="Schnellansicht">
                                            <x-heroicon-o-eye class="h-5 w-5" />
                                        </button>
                                        <a href="{{ route('shop.product', $p) }}" class="rounded-full p-2 hover:bg-gray-100"
                                            aria-label="Produkt öffnen">
                                            <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5" />
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3 class="text-base font-medium text-gray-900 truncate">
                                    <a href="{{ route('shop.product', $p) }}">{{ $p->name }}</a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ number_format((float) $p->base_price, 2, ',', '.') }} €
                                </p>
                            </div>

                            {{-- Quick View Modal (Alpine) --}}
                            <div x-cloak x-show="open" x-transition.opacity x-on:keydown.escape.window="open=false"
                                @click.self="open=false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/50"></div>

                                <div x-show="open" x-transition class="relative w-full max-w-3xl rounded-xl bg-white p-6 shadow-xl">
                                    <button @click="open=false" class="absolute right-3 top-3 rounded p-2 hover:bg-gray-100"
                                        aria-label="Schließen">
                                        <x-heroicon-o-x-mark class="h-5 w-5" />
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <img src="{{ $img }}" alt="{{ $p->name }}" class="w-full rounded">
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-semibold">{{ $p->name }}</h4>
                                            <p class="mt-1 text-gray-800">{{ number_format((float) $p->base_price, 2, ',', '.') }} €
                                            </p>

                                            @if($p->description)
                                                <p class="mt-3 text-sm text-gray-600">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($p->description), 160) }}
                                                </p>
                                            @endif

                                            @php $purchasable = $p->isPurchasable(); @endphp
                                            @unless($purchasable)
                                                <div
                                                    class="mt-3 flex items-start gap-2 rounded-md border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
                                                    <x-heroicon-o-exclamation-triangle class="mt-0.5 h-4 w-4 shrink-0" />
                                                    <div>
                                                        @if($p->available_from && $p->available_from->gt(now()))
                                                            Dieser Artikel ist <strong>ab
                                                                {{ $p->available_from->translatedFormat('d.m.Y') }}</strong> verfügbar.
                                                        @else
                                                            Dieser Artikel ist derzeit nicht verfügbar.
                                                        @endif
                                                    </div>
                                                </div>
                                            @endunless

                                            <a href="{{ route('shop.product', $p) }}"
                                                class="mt-4 inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                                                Details & Auswahl
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- /Quick View --}}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection