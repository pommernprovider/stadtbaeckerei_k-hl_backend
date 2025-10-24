@extends('layouts.shop')
@section('title', 'Danke')

@section('content')
    <section class="page-wrapper success-msg">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="block text-center" style="padding:28px 20px;">
                        <i class="tf-ion-android-checkmark-circle" style="font-size:64px; line-height:1;"></i>
                        <h2 class="mt-10">Danke für deine Bestellung!</h2>

                        <p class="text-muted">
                            Du erhältst gleich eine Bestätigung per E-Mail.
                        </p>



                        <div class="mt-25">
                            <a href="{{ route('home') }}" class="btn btn-main">Zur Startseite</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection