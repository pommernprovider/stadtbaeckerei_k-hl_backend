@extends('layouts.shop')

@section('title', 'Kategorien')

@section('content')

    {{-- Header / Breadcrumb --}}
    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Kategorien</h1>
                <nav aria-label="Breadcrumb" class="hidden sm:block">
                    <ol class="flex items-center gap-2 text-sm text-gray-600">
                        <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                        <li aria-hidden="true">/</li>
                        <li class="text-gray-900 font-medium">Kategorien</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    {{-- Kategorien-Grid --}}
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
            @if($cats->isEmpty())
                <div class="rounded-xl border border-gray-200 p-8 text-center text-gray-600">
                    Keine Kategorien verfügbar.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($cats as $c)
                        @php
                            $img = $c->getFirstMediaUrl('category_images') ?: 'https://via.placeholder.com/600x400';
                            $desc = !empty($c->description)
                                ? \Illuminate\Support\Str::limit(strip_tags($c->description), 80)
                                : '';
                        @endphp

                        <a href="{{ route('shop.products', $c) }}"
                            class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow transition">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                <img src="{{ $img }}" alt="{{ $c->name }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                            </div>
                            <div class="p-4">
                                <h3 class="text-base font-medium text-gray-900 truncate">{{ $c->name }}</h3>
                                <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ $desc ?: ' ' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection