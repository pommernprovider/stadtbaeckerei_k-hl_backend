@extends('layouts.shop')

@section('title', 'Kategorien')

@section('content')
    {{-- Breadcrumb + Pager --}}
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h1 class="page-name">Kategorien</h1>
                        <ol class="breadcrumb">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li class="active">Kategorien</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="product-category section">
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="title text-center">
                        <h2>Product Category</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    @foreach($cats as $c)
                        <div class="category-box">
                            <a href="{{ route('shop.products', $c) }}">
                                <img
                                    src="{{ $c->getFirstMediaUrl('category_images') ?: 'https://via.placeholder.com/600x400' }}" />
                                <div class="content">
                                    <h3>{{ $c->name }}</h3>
                                    <p>{{ $c->name }}</p>

                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection