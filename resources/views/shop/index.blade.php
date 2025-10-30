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
                            <div class="aspect-21/9 w-full bg-gray-100">
                                <img src="{{ $img }}" alt="{{ $c->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]">
                            </div>

                            {{-- dunkler Verlauf für Lesbarkeit --}}
                            <div
                                class="pointer-events-none absolute inset-0 bg-linear-to-t from-black/60 via-black/30 to-transparent">
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
    @include('shop.related-product', [
        'title' => 'Empfohlene Produkte',
        'products' => $featured ?? collect(),
    ])

@endsection
