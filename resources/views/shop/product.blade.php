@extends('layouts.shop')
@section('title', $product->name)

@section('content')
@php
  $rate  = (float) ($product->tax_rate ?? 0);
  $gross = (float) $product->base_price;
  $net   = $rate > 0 ? round($gross / (1 + $rate/100), 2) : $gross;
  $vat   = round($gross - $net, 2);

  // Media
  $main = $product->getFirstMediaUrl('product_main', 'web')
       ?: $product->getFirstMediaUrl('product_main')
       ?: 'https://via.placeholder.com/800x600';
  $gallery = $product->getMedia('product_gallery');
@endphp

<section class="single-product">
  <div class="container">

    {{-- Breadcrumb + Pager --}}
    <div class="row">
      <div class="col-md-6">
        <ol class="breadcrumb">
          <li><a href="{{ route('home') }}">Home</a></li>
          @if($product->category)
            <li><a href="{{ route('shop.products', $product->category) }}">{{ $product->category->name }}</a></li>
          @endif
          <li class="active">{{ $product->name }}</li>
        </ol>
      </div>
    </div>

    <div class="row mt-20">
      {{-- LEFT: Bilder/Carousel --}}
      <div class="col-md-5">
        <div class="single-product-slider">
          <div id="carousel-custom" class="carousel slide" data-ride="carousel">
            <div class="carousel-outer">
              <div class="carousel-inner">
                {{-- Main als erstes Item --}}
                <div class="item active">
                  <img class="img-responsive" src="{{ $main }}" alt="{{ $product->name }}">
                </div>
                {{-- Galerie-Items --}}
                @foreach($gallery as $m)
                  <div class="item">
                    <img class="img-responsive" src="{{ $m->getUrl('web') ?: $m->getUrl() }}" alt="{{ $product->name }}">
                  </div>
                @endforeach
              </div>

              <a class="left carousel-control" href="#carousel-custom" data-slide="prev">
                <i class="tf-ion-ios-arrow-left"></i>
              </a>
              <a class="right carousel-control" href="#carousel-custom" data-slide="next">
                <i class="tf-ion-ios-arrow-right"></i>
              </a>
            </div>

            {{-- Thumbs --}}
            <ol class="carousel-indicators mCustomScrollbar meartlab">
              <li data-target="#carousel-custom" data-slide-to="0" class="active">
                <img src="{{ $main }}" alt="{{ $product->name }}" />
              </li>
              @foreach($gallery as $i => $m)
                <li data-target="#carousel-custom" data-slide-to="{{ $i+1 }}">
                  <img src="{{ $m->getUrl('thumb') ?: $m->getUrl() }}" alt="{{ $product->name }}" />
                </li>
              @endforeach
            </ol>
          </div>
        </div>
      </div>

      {{-- RIGHT: Details + Formular --}}
      <div class="col-md-7">
        <div class="single-product-details">
          <h2>{{ $product->name }}</h2>
        @php $badge = $product->availabilityBadge(); @endphp
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:24px;">


            {{-- Tags --}}
            @foreach($product->tags as $tag)
                <span class="tag-badge">{{ $tag->name }}</span>
            @endforeach
            </div>

          <p class="product-price">
            {{ number_format($gross, 2, ',', '.') }} €
          </p>

          <div class="text-muted" style="margin-top:-8px;">
            <small>
              Alle Preise inkl. gesetzl. MwSt. ({{ number_format($rate, 2, ',', '.') }}%).<br>
              Entspricht {{ number_format($net, 2, ',', '.') }} € netto + {{ number_format($vat, 2, ',', '.') }} € MwSt. pro Stück.
            </small>
          </div>

        @if ($product->description)
            <p class="product-description mt-20">{!! nl2br(e($product->description)) !!}</p>
            @endif

            @php
            $baseDays  = (int) ($product->min_lead_days ?? 0);
            $extraHint = (int) $product->maxOptionLeadDaysHint();
            @endphp

            @if ($baseDays > 0 || $extraHint > 0)
            <div class="alert alert-info mt-15" role="alert" style="display:flex;gap:.5rem;align-items:center;">
                <i class="tf-ion-information-circled"></i>
                <div>
                @if ($baseDays > 0)
                    <div><strong>Vorlaufzeit:</strong> {{ $baseDays }} Tage</div>
                @endif
                @if ($extraHint > 0)
                    <div class="text-muted small">Je nach Auswahl kann sich die Vorlaufzeit um bis zu {{ $extraHint }} {{ Str::plural('Tag', $extraHint) }} verlängern.</div>
                @endif
                </div>
            </div>
        @endif

        @php $purchasable = $product->isPurchasable(); @endphp

        @if(!$purchasable)
        <div class="alert alert-warning" role="alert">
            <i class="tf-ion-alert-circled"></i>
            @if($product->available_from && $product->available_from->gt(now()))
            Dieser Artikel ist <strong>ab {{ $product->available_from->translatedFormat('d.m.Y') }}</strong> verfügbar.
            @else
            Dieser Artikel ist derzeit nicht verfügbar.
            @endif
        </div>
        @endif

          {{-- ADD TO CART --}}
          <form action="{{ route('cart.add') }}" method="post" class="mt-20">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Optionen --}}
            @foreach($product->options as $opt)
              <div class="form-group">
                <label class="control-label">
                  {{ $opt->name }}
                  @if($opt->is_required) <span class="text-danger">*</span> @endif
                </label>
                @php $field = "options.{$opt->id}"; @endphp

                @switch($opt->type)
                  @case('select')
                    <select name="options[{{ $opt->id }}]" class="form-control" @if($opt->is_required) required @endif>
                      <option value="">— bitte wählen —</option>
                      @foreach($opt->activeValues as $val)
                        <option value="{{ $val->id }}" @selected(old($field) == $val->id)>
                          {{ $val->value }}
                          @if((float)$val->price_delta !== 0) ({{ number_format($val->price_delta, 2, ',', '.') }} €) @endif
                        </option>
                      @endforeach
                    </select>
                    @break

                  @case('radio')
                    <div>
                      @foreach($opt->activeValues as $val)
                        <div class="radio">
                          <label>
                            <input type="radio"
                                   name="options[{{ $opt->id }}]"
                                   value="{{ $val->id }}"
                                   @checked(old($field) == $val->id)
                                   @if($opt->is_required) required @endif>
                            {{ $val->value }}
                            @if((float)$val->price_delta !== 0) ({{ number_format($val->price_delta, 2, ',', '.') }} €) @endif
                          </label>
                        </div>
                      @endforeach
                    </div>
                    @break

                  @case('multi')
                    @php $oldVals = collect(old($field, []))->map(fn($v)=>(int)$v)->all(); @endphp
                    <div>
                      @foreach($opt->activeValues as $val)
                        <div class="checkbox">
                          <label>
                            <input type="checkbox"
                                   name="options[{{ $opt->id }}][]"
                                   value="{{ $val->id }}"
                                   @checked(in_array($val->id, $oldVals, true))>
                            {{ $val->value }}
                            @if((float)$val->price_delta !== 0) ({{ number_format($val->price_delta, 2, ',', '.') }} €) @endif
                          </label>
                        </div>
                      @endforeach
                    </div>
                    @break

                  @case('text')
                    <input type="text"
                           name="options[{{ $opt->id }}]"
                           value="{{ old($field) }}"
                           class="form-control"
                           @if($opt->is_required) required @endif
                           placeholder="Bitte angeben">
                    @break
                @endswitch
              </div>
            @endforeach

            {{-- Zeilen-Notiz, wenn erforderlich --}}
            @if($product->notes_required)
              <div class="form-group">
                <label class="control-label">Notiz / Widmung <span class="text-danger">*</span></label>
                <input type="text" name="line_note" value="{{ old('line_note') }}"
                       class="form-control" maxlength="255" required
                       placeholder="z. B. 'Alles Gute, Anna!'">
              </div>
            @endif

            <div class="product-quantity">
              <div class="form-group">
                <label for="qty">Menge:&nbsp;</label>
                <div class="product-quantity-slider">
                    <input id="qty" type="number" name="qty" value="{{ old('qty',1) }}" min="1" class="form-control" style="width: 90px;">
                </div>
              </div>

            </div>
            <button class="btn btn-main mt-20" {{ $purchasable ? '' : 'disabled' }}>
                In den Warenkorb
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Tabs: Details / Zutaten / Allergene --}}
    <div class="row">
      <div class="col-xs-12">
        <div class="tabCommon mt-20">
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-details" aria-expanded="true">Details</a></li>
            <li><a data-toggle="tab" href="#tab-ingredients" aria-expanded="false">Zutaten</a></li>
            <li><a data-toggle="tab" href="#tab-allergens" aria-expanded="false">Allergene</a></li>
          </ul>
          <div class="tab-content patternbg">
            <div id="tab-details" class="tab-pane fade active in">
              @if($product->description)
                <p>{!! nl2br(e($product->description)) !!}</p>
              @else
                <p>Keine weiteren Details.</p>
              @endif
            </div>
            <div id="tab-ingredients" class="tab-pane fade">
              @if($product->ingredients)
                <p>{!! nl2br(e($product->ingredients)) !!}</p>
              @else
                <p>Keine Angaben.</p>
              @endif
            </div>
            <div id="tab-allergens" class="tab-pane fade">
              @if($product->allergens)
                <p>{!! nl2br(e($product->allergens)) !!}</p>
              @else
                <p>Keine Angaben.</p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

  </div> {{-- /.container --}}
</section>

  @include('shop.related-product', [
    'title' => 'Ähnliche Produkte',
    'products' => $related ?? collect(),
])

@endsection
