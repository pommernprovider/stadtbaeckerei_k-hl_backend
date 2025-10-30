{{-- resources/views/legal/impressum.blade.php --}}
@extends('layouts.shop')
@section('title', $title)
@section('content')
    <section class="bg-white py-12">
        <div class="mx-auto container px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold mb-6">{{ $title }}</h1>
            <div class="richtext">{!! $content !!}</div>


        </div>
    </section>
@endsection