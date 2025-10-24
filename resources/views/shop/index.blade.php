@extends('layouts.shop')

@section('title', 'Start')

@section('content')

    {{-- Hero / Intro (optional) --}}
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content text-center">
                        <h1 class="page-name">Willkommen</h1>
                        <p>Frisch gebacken. Online vorbestellen & in der Filiale abholen.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Kategorien-Grid --}}
    <section class="product-category section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="title text-center">
                        <h2>Kategorien</h2>
                    </div>
                </div>

                @forelse($cats as $c)
                    @php
                        $img = $c->getFirstMediaUrl('category_images') ?: 'https://via.placeholder.com/600x400';
                      @endphp
                    <div class="col-sm-6 col-md-12">
                        <div class="category-box">
                            <a href="{{ route('shop.products', $c) }}">
                                <img src="{{ $img }}" alt="{{ $c->name }}">
                                <div class="content">
                                    <h3 class="">{{ $c->name }}</h3>
                                    @if(!empty($c->description))
                                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($c->description), 60) }}</p>
                                    @else
                                        <p>&nbsp;</p>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-md-12">
                        <p>Keine Kategorien verfügbar.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </section>

    @include('shop.related-product', [
        'title' => 'Empfohlene Produkte',
        'products' => $featured,
    ])


@endsection
