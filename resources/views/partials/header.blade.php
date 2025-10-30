{{-- resources/views/partials/header.blade.php (hell) --}}
@php
    $cartCount = (int) ($miniCart['count'] ?? 0);
    $cartTotal = number_format((float) ($miniCart['total'] ?? 0), 2, ',', '.');
@endphp

@php
    // Einheitliche Nav-Klassen
    $navBase = 'inline-flex items-center px-3 py-2 text-sm transition';
    $navIdle = 'text-gray-700 hover:text-gray-900 hover:bg-gray-50';
    $navActive = 'text-gray-900 font-medium border-b-1 border-gray-900';


    // Helper: gibt passende Klasse zurück je nach Active-State
    $navClass = function (bool $active) use ($navBase, $navIdle, $navActive) {
        return $active ? "$navBase $navActive" : "$navBase $navIdle";
    };

    // Active-Checks:
    $isHome = request()->routeIs('home');
    $isProducts = request()->routeIs('shop.*') || request()->is('produkte*');
    $isImpressum = request()->routeIs('impressum') || request()->is('impressum');
    $isDatenschutz = request()->routeIs('datenschutz') || request()->is('datenschutz');
    $isAgb = request()->routeIs('agb') || request()->is('agb');
@endphp


<header x-data="{ open:false, prod:false, cart:false }" class="sticky top-0 z-50 border-b border-gray-200 bg-white">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex h-16 items-center justify-between">
            {{-- Left: Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-90">
                @if(!empty($brandLogo))
                    <img src="{{ $brandLogo }}" alt="{{ $branding->site_name ?? 'Stadtbäckerei Kühl' }}"
                        class="h-24 w-auto">
                @else
                    <span class="text-lg font-semibold tracking-wide text-gray-900">
                        {{ $branding->site_name ?? 'Stadtbäckerei Kühl' }}
                    </span>
                @endif
            </a>


            {{-- Center: Desktop-Nav --}}
            <nav class="hidden md:flex items-center gap-2">
                <a href="{{ route('home') }}" class="{{ $navClass($isHome) }}">
                    Start
                </a>

                {{-- Produkte Dropdown --}}
                <div class="relative" @mouseenter.window="if (window.innerWidth >= 768) prod = true"
                    @mouseleave.window="if (window.innerWidth >= 768) prod = false">
                    <button type="button" @click="prod = !prod" :aria-expanded="prod"
                        class="{{ $navClass($isProducts) }} gap-1">
                        Produkte
                        <x-heroicon-o-chevron-down class="h-4 w-4 transition" x-bind:class="prod ? 'rotate-180' : ''" />
                    </button>

                    <div x-cloak x-show="prod" x-transition.origin.top @click.outside="prod=false"
                        class="absolute left-0 mt-2 w-64 rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                        <ul class="max-h-72 overflow-auto text-sm">
                            @forelse($navProducts as $p)
                                <li>
                                    <a href="{{ route('shop.product', $p) }}"
                                        class="block rounded px-3 py-2 text-gray-800 hover:bg-gray-50">
                                        {{ $p->name }}
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-gray-500">Derzeit keine aktiven Produkte.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <a href="{{ route('impressum') }}" class="{{ $navClass($isImpressum) }}">Impressum</a>
                <a href="{{ route('datenschutz') }}" class="{{ $navClass($isDatenschutz) }}">Datenschutz</a>
                <a href="{{ route('agb') }}" class="{{ $navClass($isAgb) }}">AGB</a>
            </nav>


            {{-- Right: Icon-Buttons --}}
            <div class="flex items-center gap-2">
                {{-- Mail --}}
                @if(filled($branding->contact_email))
                    <a href="mailto:{{ $branding->contact_email }}"
                        class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        title="E-Mail">
                        <x-heroicon-o-envelope class="h-5 w-5" />
                    </a>
                @endif

                {{-- Phone --}}
                @if(filled($branding->contact_phone))
                    <a href="tel:{{ preg_replace('/\s+/', '', $branding->contact_phone) }}"
                        class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        title="Telefon">
                        <x-heroicon-o-phone class="h-5 w-5" />
                    </a>
                @endif


                {{-- Cart (Icon only) --}}
                <div class="relative" x-data @mouseenter="cart=true" @mouseleave="cart=false">
                    <button @click="cart=!cart" @keydown.escape.window="cart=false"
                        class="relative inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900"
                        :aria-expanded="cart" aria-haspopup="true" title="Warenkorb">
                        <x-heroicon-o-shopping-cart class="h-5 w-5" />
                        @if($cartCount > 0)
                            <span
                                class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Cart Dropdown --}}
                    <div x-cloak x-show="cart" x-transition.origin.top.right @click.outside="cart=false"
                        class="absolute right-0 mt-2 w-88 rounded-lg border border-gray-200 bg-white shadow-2xl">
                        @if(empty($miniCart['items']))
                            <div class="p-4 text-center text-sm text-gray-600">Dein Warenkorb ist leer.</div>
                        @else
                            <div class="max-h-80 overflow-auto divide-y divide-gray-100">
                                @foreach($miniCart['items'] as $it)
                                    <a href="{{ $it['url'] ?? '#' }}" class="flex gap-3 p-3 hover:bg-gray-50">
                                        @if(!empty($it['img']))
                                            <img src="{{ $it['img'] }}" alt="thumb" class="h-14 w-14 shrink-0 rounded object-cover"
                                                loading="lazy" decoding="async">
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-sm font-medium text-gray-900">
                                                {{ $it['name'] ?? 'Artikel' }}
                                            </div>
                                            <div class="mt-1 flex items-center justify-between text-sm text-gray-700">
                                                <div>
                                                    {{ $it['qty'] ?? 1 }} ×
                                                    {{ number_format((($it['sum'] ?? 0) / max(($it['qty'] ?? 1), 1)), 2, ',', '.') }}
                                                    €
                                                </div>
                                                <div class="font-semibold text-gray-900">
                                                    {{ number_format($it['sum'] ?? 0, 2, ',', '.') }} €
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            <div class="border-t border-gray-200 p-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Total</span>
                                    <span class="text-base font-semibold text-gray-900">{{ $cartTotal }} €</span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('cart.index') }}"
                                        class="inline-flex w-1/2 items-center justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-50">
                                        Warenkorb
                                    </a>
                                    <a href="{{ route('checkout.show') }}"
                                        class="inline-flex w-1/2 items-center justify-center rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:opacity-90">
                                        Zur Kasse
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Mobile: Burger --}}
                <button type="button"
                    class="md:hidden inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900"
                    @click="open = !open" :aria-expanded="open" aria-label="Menü öffnen/schließen">
                    <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu (hell) --}}
    <div x-cloak x-show="open" x-transition.origin.top class="md:hidden border-t border-gray-200 bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-3">
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('home') }}" class="block {{ $navClass($isHome) }}">
                        Start
                    </a>
                </li>

                {{-- Produkte Disclosure (mobil) --}}
                <li x-data="{ openProd:false }" class="border-t border-gray-200 pt-2 mt-2">
                    <button type="button" @click="openProd = !openProd"
                        class="w-full text-left {{ $navClass($isProducts) }} flex items-center justify-between">
                        <span>Produkte</span>
                        <x-heroicon-o-chevron-down class="h-4 w-4 transition"
                            x-bind:class="openProd ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openProd" x-transition.origin.top class="mt-1">
                        <ul class="max-h-60 overflow-auto rounded-md border border-gray-200 p-1">
                            @forelse($navProducts as $p)
                                <li>
                                    <a href="{{ route('shop.product', $p) }}"
                                        class="block rounded px-3 py-2 text-gray-800 hover:bg-gray-50">
                                        {{ $p->name }}
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-2 text-gray-500">Derzeit keine aktiven Produkte.</li>
                            @endforelse
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ route('impressum') }}" class="block {{ $navClass($isImpressum) }}">Impressum</a>
                </li>
                <li>
                    <a href="{{ route('datenschutz') }}" class="block {{ $navClass($isDatenschutz) }}">Datenschutz</a>
                </li>
                <li>
                    <a href="{{ route('agb') }}" class="block {{ $navClass($isAgb) }}">AGB</a>
                </li>
            </ul>

        </div>
    </div>
</header>