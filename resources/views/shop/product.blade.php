@extends('layouts.shop')
@section('title', $product->name)

@section('content')
@php
  $rate  = (float) ($product->tax_rate ?? 0);
  $gross = (float) $product->base_price;
  $net   = $rate > 0 ? round($gross / (1 + $rate/100), 2) : $gross;
  $vat   = round($gross - $net, 2);

  $main = $product->getFirstMediaUrl('product_main', 'web')
       ?: $product->getFirstMediaUrl('product_main')
       ?: '';
  $gallery = $product->getMedia('product_gallery');

  $slides = collect([$main])->merge($gallery->map(fn($m) => $m->getUrl('web') ?: $m->getUrl()))->values();
  $thumbs = collect([$main])->merge($gallery->map(fn($m) => $m->getUrl('thumb') ?: $m->getUrl()))->values();
@endphp

{{-- Header / Breadcrumb --}}
<section class="border-b border-gray-200 bg-white">
  <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-6">
    <nav aria-label="Breadcrumb" class="text-sm">
      <ol class="flex items-center gap-2 text-gray-600">
        <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
        @if($product->category)
          <li aria-hidden="true">/</li>
          <li><a href="{{ route('shop.products', $product->category) }}" class="hover:text-gray-900">{{ $product->category->name }}</a></li>
        @endif
        <li aria-hidden="true">/</li>
        <li class="text-gray-900 font-medium">{{ $product->name }}</li>
      </ol>
    </nav>
  </div>
</section>

<section class="bg-white">
  <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

      {{-- LEFT: Galerie --}}
      <div class="lg:col-span-5">
        <div
            x-data="productGallery(@js($slides))"
            x-init="init()"
            x-on:keydown.arrow-left.window="prev()"
            x-on:keydown.arrow-right.window="next()"
            class="space-y-3"
            >
        <div x-ref="stage" class="relative aspect-4/3 w-full overflow-hidden rounded-xl bg-gray-100" x-cloak>
            <template x-for="(src, i) in urls" :key="i">
            <img x-show="index===i" x-transition.opacity
                :src="src" alt="{{ $product->name }}"
                class="absolute inset-0 h-full w-full object-cover">
            </template>

            <!-- Prev/Next -->
        <button type="button" data-no-swipe @click.stop="prev()"
        class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-2 shadow hover:bg-white z-10"
        aria-label="Vorheriges Bild">
        <x-heroicon-o-chevron-left class="h-5 w-5" />
        </button>

        <button type="button" data-no-swipe @click.stop="next()"
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white/90 p-2 shadow hover:bg-white z-10"
        aria-label="Nächstes Bild">
        <x-heroicon-o-chevron-right class="h-5 w-5" />
        </button>


            <!-- Dots -->
            <div class="pointer-events-none absolute inset-x-0 bottom-2 flex justify-center gap-1">
            <template x-for="(s,i) in urls" :key="i">
                <span class="h-1.5 w-1.5 rounded-full" :class="index===i ? 'bg-white' : 'bg-white/60'"></span>
            </template>
            </div>
        </div>

          {{-- Thumbnails --}}
          <div class="flex gap-2 overflow-x-auto">
            @foreach($thumbs as $i => $t)
              <button type="button"
                class="relative h-16 w-16 shrink-0 overflow-hidden rounded border"
                :class="index === {{ $i }} ? 'border-transparent' : 'border-gray-200'"
                @click="go({{ $i }})" aria-label="Bild {{ $i+1 }} anzeigen">
                <img src="{{ $t }}" alt="Thumbnail {{ $i+1 }}" class="h-full w-full object-cover">
              </button>
            @endforeach
          </div>
        </div>
      </div>

      {{-- RIGHT: Details + Formular --}}
      <div class="lg:col-span-7">
        <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>

        {{-- Tags --}}
        @if($product->tags->isNotEmpty())
          <div class="mt-3 flex flex-wrap gap-2">
            @foreach($product->tags as $tag)
              <span class="tag-badge">{{ $tag->name }}</span>
            @endforeach
          </div>
        @endif

        {{-- Preis --}}
        <p class="mt-4 text-xl font-semibold text-gray-900">
          {{ number_format($gross, 2, ',', '.') }} €
        </p>
        <div class="mt-1 text-sm text-gray-600">
          Alle Preise inkl. gesetzl. MwSt. ({{ number_format($rate, 2, ',', '.') }}%).<br>
          Entspricht {{ number_format($net, 2, ',', '.') }} € netto + {{ number_format($vat, 2, ',', '.') }} € MwSt. pro Stück.
        </div>

        {{-- Beschreibung --}}
        @if ($product->description)
          <div class="prose prose-sm mt-5 max-w-none text-gray-700">
            {!! nl2br(e($product->description)) !!}
          </div>
        @endif

        @php
          $baseDays  = (int) ($product->min_lead_days ?? 0);
          $extraHint = (int) $product->maxOptionLeadDaysHint();
        @endphp

        {{-- Hinweis Vorlaufzeit --}}
        @if ($baseDays > 0 || $extraHint > 0)
          <div class="mt-4 flex items-start gap-2 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
            <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0" />
            <div>
              @if ($baseDays > 0)
                <div><strong>Vorlaufzeit:</strong> {{ $baseDays }} Tage</div>
              @endif
              @if ($extraHint > 0)
                <div class="text-blue-900/70">Je nach Auswahl kann sich die Vorlaufzeit um bis zu {{ $extraHint }} {{ Str::plural('Tag', $extraHint) }} verlängern.</div>
              @endif
            </div>
          </div>
        @endif

     {{-- Verfügbarkeit --}}
    @php $purchasable = $product->isPurchasable(); @endphp
    @unless($purchasable)
    <div class="mt-4 flex items-start gap-2 rounded-md border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
        <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0" />
        <div>
        @php
            $from = $product->available_from;
            $until = $product->available_until;
            $fmt = fn($d) => optional($d)?->translatedFormat('d.m.Y');
        @endphp

        @if ($from && $until)
            Verfügbar <strong>vom {{ $fmt($from) }} bis {{ $fmt($until) }}</strong>.
        @elseif ($from)
            Verfügbar <strong>ab {{ $fmt($from) }}</strong>.
        @else
            Dieser Artikel ist derzeit nicht verfügbar.
        @endif
        </div>
    </div>
    @endunless


        {{-- ADD TO CART --}}
        <form action="{{ route('cart.add') }}" method="post" class="mt-6">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">

          {{-- Optionen --}}
          <div class="space-y-5">
            @foreach($product->options as $opt)
              @php $field = "options.{$opt->id}"; @endphp
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-900">
                  {{ $opt->name }} @if($opt->is_required) <span class="text-red-600">*</span> @endif
                </label>

                @switch($opt->type)
                @case('select')
                    <select name="options[{{ $opt->id }}]"
                            class="block w-full rounded-md border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900"
                            @if($opt->is_required) required @endif>
                    <option value="">— bitte wählen —</option>
                    @foreach($opt->activeValues as $val)
                        <option value="{{ $val->id }}" @selected(old($field) == $val->id)>
                        {{ $val->value }}
                        @if($val->price_delta && (float)$val->price_delta > 0)
                            (+{{ number_format($val->price_delta, 2, ',', '.') }} €)
                        @endif
                        </option>
                    @endforeach
                    </select>
                    @break

                @case('radio')
                    <div class="space-y-2">
                    @foreach($opt->activeValues as $val)
                        <label class="flex items-center gap-2 text-sm text-gray-800">
                        <input type="radio" name="options[{{ $opt->id }}]" value="{{ $val->id }}"
                                class="h-4 w-4 border-gray-300 text-gray-900 focus:ring-gray-900"
                                @checked(old($field) == $val->id)
                                @if($opt->is_required) required @endif>
                        <span>
                            {{ $val->value }}
                            @if($val->price_delta && (float)$val->price_delta > 0)
                            (+{{ number_format($val->price_delta, 2, ',', '.') }} €)
                            @endif
                        </span>
                        </label>
                    @endforeach
                    </div>
                    @break

                @case('multi')
                    @php $oldVals = collect(old($field, []))->map(fn($v)=>(int)$v)->all(); @endphp
                    <div class="space-y-2">
                    @foreach($opt->activeValues as $val)
                        <label class="flex items-center gap-2 text-sm text-gray-800">
                        <input type="checkbox" name="options[{{ $opt->id }}][]" value="{{ $val->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                                @checked(in_array($val->id, $oldVals, true))>
                        <span>
                            {{ $val->value }}
                            @if($val->price_delta && (float)$val->price_delta > 0)
                            (+{{ number_format($val->price_delta, 2, ',', '.') }} €)
                            @endif
                        </span>
                        </label>
                    @endforeach
                    </div>
                    @break

                @case('text')
                    <input type="text" name="options[{{ $opt->id }}]"
                        value="{{ old($field) }}"
                        class="block w-full rounded-md border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900"
                        @if($opt->is_required) required @endif
                        placeholder="Bitte angeben">
                    @break
                @endswitch

              </div>
            @endforeach
          </div>

          {{-- Zeilen-Notiz --}}
          @if($product->notes_required)
            <div class="mt-5">
              <label class="mb-2 block text-sm font-medium text-gray-900">
                Notiz / Widmung <span class="text-red-600">*</span>
              </label>
              <input type="text" name="line_note" value="{{ old('line_note') }}"
                     maxlength="255" required
                     placeholder="z. B. 'Alles Gute, Anna!'"
                     class="block w-full rounded-md border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
            </div>
          @endif

          {{-- Menge + Button --}}
          <div class="mt-6 flex items-end gap-4">
            <div>
              <label for="qty" class="mb-1 block text-sm font-medium text-gray-900">Menge</label>
              <input id="qty" type="number" name="qty" value="{{ old('qty',1) }}" min="1"
                     class="w-24 rounded-md border p-2 border-gray-300 text-sm focus:border-gray-900 focus:ring-gray-900">
            </div>

            <button
              class="inline-flex items-center rounded-md bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:opacity-90 disabled:opacity-50">
              In den Warenkorb
            </button>
          </div>
        </form>


      </div>
    </div>
        {{-- Tabs: Details / Zutaten / Allergene (Alpine) --}}
        <div x-data="{ tab:'details' }" class="mt-10">
          <div class="flex gap-2 border-b border-gray-200">
            <button @click="tab='details'" :class="tab==='details' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600'"
              class="border-b-2 px-3 py-2 text-sm font-medium">Details</button>
            <button @click="tab='ingredients'" :class="tab==='ingredients' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600'"
              class="border-b-2 px-3 py-2 text-sm font-medium">Zutaten</button>
            <button @click="tab='allergens'" :class="tab==='allergens' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-600'"
              class="border-b-2 px-3 py-2 text-sm font-medium">Allergene</button>
          </div>

          <div class="mt-4 text-sm text-gray-800">
            <div x-show="tab==='details'">
              @if($product->description)
                <p>{!! nl2br(e($product->description)) !!}</p>
              @else
                <p>Keine weiteren Details.</p>
              @endif
            </div>
            <div x-show="tab==='ingredients'">
              @if($product->ingredients)
                <p>{!! nl2br(e($product->ingredients)) !!}</p>
              @else
                <p>Keine Angaben.</p>
              @endif
            </div>
            <div x-show="tab==='allergens'">
              @if($product->allergens)
                <p>{!! nl2br(e($product->allergens)) !!}</p>
              @else
                <p>Keine Angaben.</p>
              @endif
            </div>
          </div>
        </div>
  </div>
</section>

    {{-- Ähnliche Produkte --}}
    @include('shop.related-product', [
      'title' => 'Ähnliche Produkte',
      'products' => $related ?? collect(),
    ])

<script>
  window.productGallery = function (urls) {
    return {
      urls: Array.isArray(urls) ? urls.filter(Boolean) : [],
      index: 0,
      get count() { return this.urls.length || 1; },

      init() {
        if (this.index >= this.count) this.index = 0;
        const el = this.$refs.stage;
        if (!el) return;

        let startX = null;

        el.addEventListener('pointerdown', (e) => {
          if (e.target.closest('[data-no-swipe]')) return;
          startX = e.clientX;
          try { el.setPointerCapture(e.pointerId); } catch {}
        });

        el.addEventListener('pointerup', (e) => {
          if (startX === null) return;
          if (e.target.closest('[data-no-swipe]')) { startX = null; return; }
          const dx = e.clientX - startX;
          if (Math.abs(dx) > 40) (dx < 0 ? this.next() : this.prev());
          startX = null;
        });

        el.addEventListener('click', (e) => {

          if (e.target.closest('[data-no-swipe]')) return;
        }, { passive: true });

        el.tabIndex = 0;
      },

      prev() { this.index = (this.index - 1 + this.count) % this.count; },
      next() { this.index = (this.index + 1) % this.count; },
      go(i)  { if (i >= 0 && i < this.count) this.index = i; },
    };
  };
</script>


@endsection
