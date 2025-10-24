@props([
    'title' => 'Empfohlene Produkte',
    'products' => collect(),
])

@if($products->isNotEmpty())
<section class="products related-products section">
    <div class="container">
        <div class="row">
            <div class="title text-center">
                <h2>{{ $title }}</h2>
            </div>
        </div>

        <div class="row">
            @foreach($products as $p)
                @php
                    $img = $p->getFirstMediaUrl('product_main', 'web')
                        ?: $p->getFirstMediaUrl('product_main')
                        ?: 'https://via.placeholder.com/600x400';
                    $modalId = 'related-modal-'.$p->id;

                    $badge = $p->availabilityBadge();
                    $ribbonText = $badge ? $badge['text'] : ($p->visibility_status === 'seasonal' ? 'Saisonal' : null);
                @endphp

                <div class="col-sm-6 col-md-3">
                    <div class="product-item">
                        <div class="product-thumb">
                            {{-- Ribbon oben rechts --}}
                            @if($ribbonText)
                                <div class="ribbon">
                                    <span>{{ $ribbonText }}</span>
                                </div>
                            @endif

                            {{-- Einheitliches Bildformat via Aspect-Ratio-Box --}}
                            <a href="{{ route('shop.product', $p) }}" class="media-box">
                                <img class="img-responsive" src="{{ $img }}" alt="{{ $p->name }}" loading="lazy" decoding="async">
                            </a>

                            {{-- Tags unten rechts (pink) --}}
                            <div class="tag-badges">
                                @foreach(($p->relationLoaded('tags') ? $p->tags : $p->tags()->limit(3)->get()) as $tag)
                                    <span class="tag-badge" title="{{ $tag->name }}">{{ $tag->name }}</span>
                                @endforeach
                            </div>

                            {{-- Aktionen (Auge/Link) unverändert --}}
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
                            <p class="price">{{ number_format((float)$p->base_price, 2, ',', '.') }} €</p>
                        </div>
                    </div>
                </div>

                {{-- Preview-Modal ohne Quick-Add --}}
                <div class="modal product-modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="{{ $modalId }}-title">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="tf-ion-close"></i>
                    </button>
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-8 col-sm-6">
                                        <div class="modal-image">
                                            <img class="img-responsive" src="{{ $img }}" alt="{{ $p->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="product-short-details">
                                            <h2 id="{{ $modalId }}-title" class="product-title">{{ $p->name }}</h2>
                                            <p class="product-price">{{ number_format((float)$p->base_price, 2, ',', '.') }} €</p>
                                            @if($p->description)
                                                <p class="product-short-description">
                                                    {{ \Illuminate\Support\Str::limit(strip_tags($p->description), 160) }}
                                                </p>
                                            @endif
                                            <a href="{{ route('shop.product', $p) }}" class="btn btn-main">Details & Auswahl</a>
                                        </div>
                                    </div>
                                </div> {{-- /.row --}}
                            </div> {{-- /.modal-body --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
