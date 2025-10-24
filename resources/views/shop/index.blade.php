@extends('layouts.shop')

@section('title', 'Start')

@section('content')

    {{-- Hero --}}
    {{-- @includeIf('partials.hero') --}}

    {{-- Kategorien --}}
    <section class="bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-10">
            @if($cats->isEmpty())
                <div class="mt-6 rounded-lg border border-gray-200 p-6 text-center text-gray-600">
                    Keine Kategorien verfügbar.
                </div>
            @else
                {{-- Promo-Banner statt Grid-Karten --}}
                <div class="mt-6 space-y-6">
                    @foreach($cats as $c)
                        @php
                            $img = $c->getFirstMediaUrl('category_images') ?: 'https://via.placeholder.com/1600x700';
                            $desc = !empty($c->description) ? \Illuminate\Support\Str::limit(strip_tags($c->description), 140) : '';
                        @endphp

                        <a href="{{ route('shop.products', $c) }}"
                            class="group relative block overflow-hidden rounded-2xl border border-gray-200 shadow-sm hover:shadow transition">
                            {{-- großes Banner (21:9) --}}
                            <div class="aspect-[21/9] w-full bg-gray-100">
                                <img src="{{ $img }}" alt="{{ $c->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                            </div>

                            {{-- dunkler Verlauf für Lesbarkeit --}}
                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent">
                            </div>

                            {{-- Text & CTA unten links --}}
                            <div class="absolute inset-0 p-6 sm:p-10 flex items-end">
                                <div class="max-w-2xl">
                                    <h3 class="text-2xl sm:text-3xl font-semibold text-white drop-shadow">{{ $c->name }}</h3>
                                    @if($desc)
                                        <p class="mt-2 hidden sm:block text-white/90">{{ $desc }}</p>
                                    @endif
                                    <span
                                        class="mt-4 inline-flex items-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-900 transition group-hover:opacity-90">
                                        Jetzt entdecken
                                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

            @endif
        </div>
    </section>

    {{-- Empfohlene Produkte --}}
    <section class="bg-white py-12 xl:py-24">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 pb-12">
            <div class="flex items-end justify-between mb-12 xl:mb-24">
                <h2 class="text-3xl text-center mx-auto font-semibold">Empfohlene Produkte</h2>
            </div>

            @if(($featured ?? collect())->isEmpty())
                <div class="mt-6 rounded-lg border border-gray-200 p-6 text-center text-gray-600">
                    Aktuell keine Empfehlungen.
                </div>
            @else
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featured as $p)
                        @php
                            $img = $p->getFirstMediaUrl('product_main', 'web')
                                ?: $p->getFirstMediaUrl('product_main')
                                ?: 'https://via.placeholder.com/800x600';
                          @endphp

                        <div class="group">
                            {{-- Bild (alleiniger „Card“-Look) --}}
                            <a href="{{ route('shop.product', $p) }}" class="block overflow-hidden rounded-xl bg-gray-100">
                                <div class="aspect-[4/3] w-full">
                                    <img src="{{ $img }}" alt="{{ $p->name }}" loading="lazy" decoding="async"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                                </div>
                            </a>

                            {{-- Text unter dem Bild (ohne Preis) --}}
                            <h3 class="mt-3 text-center text-lg font-medium text-gray-900 line-clamp-2">
                                <a href="{{ route('shop.product', $p) }}" class="hover:underline">
                                    {{ $p->name }}
                                </a>
                            </h3>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

@endsection