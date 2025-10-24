{{-- resources/views/partials/footer.blade.php --}}
<footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto container px-4 sm:px-6 lg:px-8">

        {{-- TOP: Logo zentriert + Socials --}}
        <div class="py-10">
            <div class="flex flex-col items-center gap-6">

                {{-- Logo / Brand --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @php
                        // Falls du ein Bildlogo hast:
                        // $logo = asset('images/logo-footer.svg');
                        $logo = null;
                      @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="Stadtbäckerei Kühl" class="h-10 w-auto">
                    @else
                        <span class="text-lg font-semibold text-gray-900">Stadtbäckerei Kühl</span>
                    @endif
                </a>

                {{-- Socials (eingebettete SVG Marken-Icons) --}}
                <div class="flex items-center justify-center gap-4">
                    <a href="https://www.facebook.com/themefisher" class="text-gray-600 hover:text-gray-900"
                        aria-label="Facebook" target="_blank" rel="noopener">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M22 12.06C22 6.49 17.52 2 11.94 2S2 6.49 2 12.06c0 5.01 3.66 9.16 8.45 9.94v-7.03H7.9v-2.91h2.55V9.41c0-2.52 1.5-3.92 3.8-3.92 1.1 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.91h-2.34V22c4.79-.78 8.45-4.93 8.45-9.94Z" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/themefisher" class="text-gray-600 hover:text-gray-900"
                        aria-label="Instagram" target="_blank" rel="noopener">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5Zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5Zm5.75-3.25a.75.75 0 1 1-.75.75.75.75 0 0 1 .75-.75Z" />
                        </svg>
                    </a>
                    <a href="https://www.twitter.com/themefisher" class="text-gray-600 hover:text-gray-900"
                        aria-label="X (Twitter)" target="_blank" rel="noopener">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M3 3h3.7l5.01 6.69L16.8 3H21l-7.52 9.67L21.5 21h-3.7l-5.4-7.2L7.2 21H3l7.98-10.26L3 3Z" />
                        </svg>
                    </a>
                    <a href="https://www.pinterest.com/themefisher/" class="text-gray-600 hover:text-gray-900"
                        aria-label="Pinterest" target="_blank" rel="noopener">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 2C6.48 2 3 5.64 3 9.88c0 2.26 1.01 4.27 3.18 5 .35.12.66 0 .76-.38.07-.27.24-.95.31-1.24.1-.37.06-.5-.21-.82-.62-.73-1.02-1.67-1.02-3.01 0-3.87 2.91-7.34 7.57-7.34 4.12 0 6.89 2.51 6.89 6.08 0 4.77-2.11 8.81-5.23 8.81-1.72 0-3.01-1.42-2.6-3.17.49-2.05 1.44-4.26 1.44-5.74 0-1.33-.72-2.45-2.2-2.45-1.75 0-3.15 1.81-3.15 4.22 0 1.54.52 2.58.52 2.58l-2.08 8.83c-.62 2.62.09 5.83.1 5.96h.02c.02 0 2.2-2.99 2.85-5.64.19-.74 1.09-4.33 1.09-4.33.54 1.03 2.12 1.94 3.8 1.94 5 0 8.64-4.69 8.64-10.96C21 5.23 17.1 2 12 2Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- BOTTOM: Links/Copy in einer Linie --}}
        <div class="border-t border-gray-200 py-4">
            <div class="flex flex-col items-center gap-3 text-xs text-gray-600 sm:flex-row sm:justify-between">
                {{-- Links unten: Copyright --}}
                <div class="order-2 sm:order-1">
                    2025 &copy;
                    <a href="https://pommernprovider.de/" target="_blank" rel="noopener"
                        class="underline hover:no-underline">Pommernprovider</a>
                </div>

                {{-- Rechts unten: Impressum & Datenschutz --}}
                <nav class="order-1 sm:order-2">
                    <ul class="flex items-center gap-4">
                        @if (Route::has('impressum'))
                            <li><a href="{{ route('impressum') }}" class="hover:text-gray-900">Impressum</a></li>
                        @endif
                        @if (Route::has('privacy'))
                            <li><a href="{{ route('privacy') }}" class="hover:text-gray-900">Datenschutz</a></li>
                        @endif

                        {{-- Fallback, falls Routen noch nicht existieren --}}
                        @if (!Route::has('impressum') && !Route::has('privacy'))
                            <li><a href="{{ route('home') }}" class="hover:text-gray-900">Impressum &amp; Datenschutz</a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</footer>