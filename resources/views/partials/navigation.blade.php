<section class="menu">
    <nav class="navbar navigation">
        <div class="container">
            <div class="navbar-header">
                <h2 class="menu-title">Main Menu</h2>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <div id="navbar" class="navbar-collapse collapse text-center">
                <ul class="nav navbar-nav">

                    <li><a href="{{ route('home') }}">Start</a></li>

                    <!-- Blog -->
                    <li class="dropdown dropdown-slide">
                        <a href="#!" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                            data-delay="350" role="button" aria-haspopup="true" aria-expanded="false">Produkte <span
                                class="tf-ion-ios-arrow-down"></span></a>


                        <ul class="dropdown-menu">
                            @forelse($navProducts as $p)
                                <li><a href="{{ route('shop.product', $p) }}">{{ $p->name }}</a></li>
                            @empty
                                <li class="text-muted">Derzeit keine aktiven Produkte.</li>
                            @endforelse
                        </ul>
                    </li><!-- / Blog -->



                    <li><a href="{{ route('home') }}">Impressum & Datenschutz</a></li>
                </ul>
            </div>
        </div>
    </nav>
</section>