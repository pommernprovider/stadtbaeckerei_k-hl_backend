{{-- resources/views/shop/thanks.blade.php --}}
@extends('layouts.shop')
@section('title', 'Danke')

@section('content')

    {{-- Header / Breadcrumb --}}
    <section class="border-b border-gray-200 bg-white">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-semibold text-gray-900">Bestellung abgeschlossen</h1>
            <nav aria-label="Breadcrumb" class="mt-2 text-sm">
                <ol class="flex items-center gap-2 text-gray-600">
                    <li><a href="{{ route('home') }}" class="hover:text-gray-900">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-gray-900 font-medium">Danke</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-10 text-center">
                {{-- Check Icon --}}
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                    <svg class="h-8 w-8 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h2 class="mt-6 text-2xl font-semibold text-gray-900">Danke für deine Bestellung!</h2>
                <p class="mt-2 text-sm text-gray-700">
                    Du erhältst in Kürze eine Bestätigung per E-Mail.
                </p>


                <div class="mt-8 flex items-center justify-center gap-3">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-50">
                        Zur Startseite
                    </a>

                </div>
            </div>
        </div>
    </section>

@endsection