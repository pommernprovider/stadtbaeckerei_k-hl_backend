@extends('layouts.shop')

@section('title', $category->name)

@section('content')

    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h1 class="page-name">{{ $category->name }}</h1>
                        <ol class="breadcrumb">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li class="active">{{ $category->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="products section">
        <div class="container">
            <div class="row">

                @forelse($products as $p)
                    @php
                        $img = $p->getFirstMediaUrl('product_main', 'web')
                            ?: $p->getFirstMediaUrl('product_main')
                            ?: 'https://via.placeholder.com/600x400';
                        $modalId = 'product-modal-' . $p->id;
                    @endphp

                    <div class="col-sm-6 col-md-4">
                        <div class="product-item">
                            <div class="product-thumb">
                                {{-- Ribbon oben rechts (Saisonal oder availabilityBadge) --}}
                                @php
                                    $badge = $p->availabilityBadge();
                                    $ribbonText = $badge ? $badge['text'] : ($p->visibility_status === 'seasonal' ? 'Saisonal' : null);
                                @endphp
                                @if($ribbonText)
                                    <div class="ribbon">
                                        <span>{{ $ribbonText }}</span>
                                    </div>
                                @endif

                                <a href="{{ route('shop.product', $p) }}" class="media-box">
                                    <img class="img-responsive" src="{{ $img }}" alt="{{ $p->name }}" loading="lazy"
                                        decoding="async">
                                </a>

                                {{-- Tags unten rechts (pink) --}}
                                <div class="tag-badges">
                                    @foreach(($p->relationLoaded('tags') ? $p->tags : $p->tags()->limit(3)->get()) as $tag)
                                        <span class="tag-badge" title="{{ $tag->name }}">{{ $tag->name }}</span>
                                    @endforeach
                                </div>

                                {{-- Preview/Aktionen bleiben unverändert --}}
                                <div class="preview-meta">
                                    <ul>
                                        <li>
                                            <a href="#{{ $modalId }}" data-toggle="modal" data-target="#{{ $modalId }}">
                                                <i class="tf-ion-eye"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('shop.product', $p) }}">
                                                <i class="tf-ion-android-open"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="product-content">
                                <h4><a href="{{ route('shop.product', $p) }}">{{ $p->name }}</a></h4>
                                <p class="price">{{ number_format((float) $p->base_price, 2, ',', '.') }} €</p>
                            </div>
                        </div>

                    </div>

                    {{-- Quick View Modal (einzigartig pro Produkt) --}}
                    <div class="modal product-modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true"
                        aria-labelledby="{{ $modalId }}-title">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="tf-ion-close"></i>
                        </button>
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-8 col-sm-6 col-xs-12">
                                            <div class="modal-image">
                                                <img class="img-responsive" src="{{ $img }}" alt="{{ $p->name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="product-short-details">
                                                <h2 id="{{ $modalId }}-title" class="product-title">{{ $p->name }}</h2>
                                                <p class="product-price">
                                                    {{ number_format((float) $p->base_price, 2, ',', '.') }} €
                                                </p>

                                                @if($p->description)
                                                    <p class="product-short-description">
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($p->description), 160) }}
                                                    </p>
                                                @endif


                                                @php $purchasable = $p->isPurchasable(); @endphp

                                                @if(!$purchasable)
                                                    <div class="alert alert-warning" role="alert">
                                                        <i class="tf-ion-alert-circled"></i>
                                                        @if($p->available_from && $p->available_from->gt(now()))
                                                            Dieser Artikel ist <strong>ab
                                                                {{ $p->available_from->translatedFormat('d.m.Y') }}</strong> verfügbar.
                                                        @else
                                                            Dieser Artikel ist derzeit nicht verfügbar.
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Nur Weiter zur Detailseite --}}
                                                <a href="{{ route('shop.product', $p) }}" class="btn btn-main">
                                                    Details & Auswahl
                                                </a>
                                            </div>
                                        </div>
                                    </div> <!-- /.row -->
                                </div> <!-- /.modal-body -->
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-md-12">
                        <p>Keine Produkte in dieser Kategorie.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </section>

@endsection