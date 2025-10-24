@props([
  'title' => 'Empfohlene Produkte',
  'products' => collect(),
])

@if($products->isNotEmpty())
<section class="bg-white py-12 xl:py-24">
  <div class="mx-auto container px-4 sm:px-6 lg:px-8 ">
    <div class="text-center mb-12 xl:mb-24">
      <h2 class="text-2xl font-semibold">{{ $title }}</h2>
    </div>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach($products as $p)
        @php
          $img = $p->getFirstMediaUrl('product_main', 'web')
              ?: $p->getFirstMediaUrl('product_main')
              ?: 'https://via.placeholder.com/600x400';

          $badge = $p->availabilityBadge();
          $ribbonText = $badge ? $badge['text'] : ($p->visibility_status === 'seasonal' ? 'Saisonal' : null);
          $tags = $p->relationLoaded('tags') ? $p->tags : $p->tags()->limit(3)->get();
        @endphp

        <div x-data="{ open:false }" class="group">
            {{-- Bild + Badges + Overlay (die "Karte") --}}
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition group-hover:shadow">
                <a href="{{ route('shop.product', $p) }}" class="block">
                <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
                    <img
                    src="{{ $img }}"
                    alt="{{ $p->name }}"
                    loading="lazy" decoding="async"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                    width="800" height="600"
                    >
                </div>
                </a>

                {{-- Ribbon oben rechts --}}
                @if($ribbonText)
                <div class="ribbon"><span>{{ $ribbonText }}</span></div>
                @endif

                {{-- Tags --}}
                @if($tags->isNotEmpty())
                <div class="tag-badges">
                    @foreach($tags as $tag)
                    <span class="tag-badge" title="{{ $tag->name }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Overlay-Actions --}}
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition group-hover:opacity-100">
                <div class="pointer-events-auto flex gap-2 rounded-full bg-white/90 p-2 shadow">
                    <button @click="open=true" class="rounded-full p-2 hover:bg-gray-100" aria-label="Schnellansicht">
                    <x-heroicon-o-eye class="h-5 w-5" />
                    </button>
                    <a href="{{ route('shop.product', $p) }}" class="rounded-full p-2 hover:bg-gray-100" aria-label="Produkt öffnen">
                    <x-heroicon-o-arrow-top-right-on-square class="h-5 w-5" />
                    </a>
                </div>
                </div>
            </div>

            {{-- Text UNTER dem Bild --}}
            <div class="mt-3 px-1 text-center">
                <h4 class="text-base font-medium text-gray-900 line-clamp-2">
                <a href="{{ route('shop.product', $p) }}" class="hover:underline">{{ $p->name }}</a>
                </h4>
                <p class="mt-1 text-sm text-gray-700">
                {{ number_format((float)$p->base_price, 2, ',', '.') }} €
                </p>
            </div>

            {{-- Quick View Modal (unverändert) --}}
            <div x-cloak x-show="open" x-transition.opacity x-on:keydown.escape.window="open=false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50"></div>

                <div x-show="open" x-transition class="relative w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                <button @click="open=false" class="absolute right-3 top-3 rounded p-2 hover:bg-gray-100" aria-label="Schließen">
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                    <div class="aspect-[4/3] w-full overflow-hidden rounded bg-gray-100">
                        <img src="{{ $img }}" alt="{{ $p->name }}" class="h-full w-full object-cover">
                    </div>
                    </div>
                    <div >
                    <h3 class="text-xl font-semibold">{{ $p->name }}</h3>
                    <p class="mt-1 text-gray-800">{{ number_format((float)$p->base_price, 2, ',', '.') }} €</p>

                    @if($p->description)
                        <p class="mt-3 text-sm text-gray-600">
                        {{ \Illuminate\Support\Str::limit(strip_tags($p->description), 160) }}
                        </p>
                    @endif

                    <a href="{{ route('shop.product', $p) }}"
                        class="mt-4 inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                        Details & Auswahl
                    </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
