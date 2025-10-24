{{-- resources/views/partials/navigation.blade.php --}}
<nav class="border-b border-gray-200 bg-white" x-data="{ open:false, prod:false }">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            {{-- Left: Brand / Menü-Titel optional --}}
            <div class="flex items-center gap-3">
                <h2 class="sr-only">Hauptmenü</h2>
                {{-- Platz für ein kleines Sub-Logo oder Slogan (optional) --}}
            </div>

            {{-- Desktop-Nav --}}
            <ul class="hidden md:flex items-center gap-6 text-sm">
                <li>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center py-2 hover:text-gray-900 {{ request()->routeIs('home') ? 'text-gray-900 font-medium' : 'text-gray-600' }}">
                        Start
                    </a>
                </li>

                {{-- Produkte: Dropdown (hover auf Desktop, click auf Mobile handled separat) --}}
                <li class="relative" @mouseenter.window="if (window.innerWidth >= 768) prod = true"
                    @mouseleave.window="if (window.innerWidth >= 768) prod = false">
                    <button type="button" @click="prod = !prod" :aria-expanded="prod"
                        class="inline-flex items-center gap-1 py-2 text-gray-600 hover:text-gray-900">
                        Produkte
                        <span class="inline-block transition" :class="prod ? 'rotate-180' : ''">
                            <i class="tf-ion-ios-arrow-down"></i>
                        </span>
                    </button>

                    {{-- Dropdown-Panel --}}
                    <div x-cloak x-show="prod" x-transition.origin.top.left @click.outside="prod=false"
                        class="absolute left-0 z-40 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-2 shadow-lg">
                        <ul class="max-h-72 overflow-auto text-sm">
                            @forelse($navProducts as $p)
                                <li>
                                    <a href="{{ route('shop.product', $p) }}"
                                        class="block rounded px-3 py-2 hover:bg-gray-50">
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
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center py-2 hover:text-gray-900 {{ request()->is('impressum*') ? 'text-gray-900 font-medium' : 'text-gray-600' }}">
                        Impressum & Datenschutz
                    </a>
                </li>
            </ul>

            {{-- Mobile: Hamburger --}}
            <button type="button" @click="open = !open" :aria-expanded="open"
                class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                aria-controls="mobile-menu" aria-label="Menü öffnen/schließen">
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile-Menü --}}
    <div x-cloak x-show="open" x-transition.origin.top id="mobile-menu"
        class="md:hidden border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('home') }}"
                        class="block rounded px-3 py-2 {{ request()->routeIs('home') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                        Start
                    </a>
                </li>

                {{-- Produkte als Disclosure im Mobile-Menü --}}
                <li x-data="{ openProd:false }" class="border-t border-gray-100 pt-2 mt-2">
                    <button type="button" @click="openProd = !openProd"
                        class="flex w-full items-center justify-between rounded px-3 py-2 text-left text-gray-700 hover:bg-gray-50">
                        <span>Produkte</span>
                        <span class="ml-2 inline-block transition" :class="openProd ? 'rotate-180' : ''">
                            <i class="tf-ion-ios-arrow-down"></i>
                        </span>
                    </button>
                    <div x-show="openProd" x-transition.origin.top class="mt-1">
                        <ul class="max-h-60 overflow-auto rounded-md border border-gray-200 p-1">
                            @forelse($navProducts as $p)
                                <li>
                                    <a href="{{ route('shop.product', $p) }}"
                                        class="block rounded px-3 py-2 text-gray-700 hover:bg-gray-50">
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
                    <a href="{{ route('home') }}"
                        class="block rounded px-3 py-2 {{ request()->is('impressum*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                        Impressum & Datenschutz
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>