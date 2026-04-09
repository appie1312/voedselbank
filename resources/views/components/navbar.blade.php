<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-success" href="{{ route('home') }}">
            <span class="rounded-circle border border-success-subtle bg-success-subtle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">VB</span>
            <span>Voedselbank Maaskantje</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>


                @auth
                                <li class="nav-item">
                    <a class="nav-link" href="{{ route('voorraad') }}">Voorraad</a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('leveranciers.index') }}">Leveranciers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard.redirect') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.edit') }}">Profiel</a>
                    </li>

                    @if (auth()->user()->isDirectie())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('klanten.index') }}">Klanten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('allergieen.index') }}">Allergieen</a>
                        </li>
                    @endif

                    <li class="nav-item ms-lg-2">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Uitloggen</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Inloggen</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-success btn-sm ms-lg-2" href="{{ route('register.form') }}">Registreren</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
