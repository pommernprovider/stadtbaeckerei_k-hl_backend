<!-- Start Top Header Bar -->
<section class="top-header">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-xs-12 col-sm-4">
                <div class="contact-number">
                    <i class="tf-ion-ios-telephone"></i>
                    <span>+49 (38326) 661-0</span>
                </div>
            </div>
            <div class="col-md-4 col-xs-12 col-sm-4">
                <!-- Site Logo -->
                <div class="logo text-center">
                    <a href="{{ route('home') }}">
                        Stadtbäckerei Kühl
                    </a>
                </div>
            </div>
            <div class="col-md-4 col-xs-12 col-sm-4">
                <!-- Cart -->
                <ul class="top-menu text-right list-inline">
                    <li class="dropdown cart-nav dropdown-slide">
                        <a href="{{ route('cart.index') }}" class="dropdown-toggle" data-toggle="dropdown"
                            data-hover="dropdown">
                            <i class="tf-ion-android-cart"></i>
                            Warenkorb
                            @if(($miniCart['count'] ?? 0) > 0)
                                <span class="badge"
                                    style="background:#e74c3c; margin-left:6px;">{{ $miniCart['count'] }}</span>
                            @endif
                        </a>

                        <div class="dropdown-menu cart-dropdown">
                            @if(empty($miniCart['items']))
                                <div class="text-center" style="padding:15px 20px;">Dein Warenkorb ist leer.</div>
                            @else
                                @foreach($miniCart['items'] as $it)
                                    <div class="media">
                                        @if(!empty($it['img']))
                                            <a class="pull-left" href="{{ $it['url'] }}">
                                                <img class="media-object" src="{{ $it['img'] }}" alt="thumb"
                                                    style="width:60px;height:60px;object-fit:cover;" />
                                            </a>
                                        @endif
                                        <div class="media-body">
                                            <h4 class="media-heading" style="margin-top:0;">
                                                <a href="{{ $it['url'] }}">{{ $it['name'] }}</a>
                                            </h4>
                                            <div class="cart-price">
                                                <span>{{ $it['qty'] }} ×</span>
                                                <span>{{ number_format(($it['sum'] / max($it['qty'], 1)), 2, ',', '.') }}
                                                    €</span>
                                            </div>
                                            <h5><strong>{{ number_format($it['sum'], 2, ',', '.') }} €</strong></h5>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="cart-summary">
                                    <span>Total</span>
                                    <span class="total-price">{{ number_format($miniCart['total'], 2, ',', '.') }} €</span>
                                </div>
                                <ul class="text-center cart-buttons">
                                    <li><a href="{{ route('cart.index') }}" class="btn btn-small">Warenkorb</a></li>
                                    <li><a href="{{ route('checkout.show') }}" class="btn btn-small btn-solid-border">Zur
                                            Kasse</a></li>
                                </ul>
                            @endif
                        </div>
                    </li>
                </ul>
                <!-- /Cart -->

            </div>
        </div>
    </div>
</section><!-- End Top Header Bar -->