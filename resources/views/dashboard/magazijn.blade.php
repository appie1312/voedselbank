@extends('layouts.app')

@section('title', 'Magazijn Dashboard')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-1">Magazijn Dashboard</h1>
        <p class="text-secondary mb-0">Jouw werkzaamheden voor voorraad en distributie.</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h5">Vandaag</h2>
                    <ul class="mb-0">
                        <li>Inkomende leveringen controleren</li>
                        <li>Koelproducten registreren</li>
                        <li>Uitgifteboxen klaarzetten</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h5">Mijn profiel</h2>
                    <p class="mb-1"><strong>Naam:</strong> {{ $profiel->name ?? auth()->user()->name }}</p>
                    <p class="mb-1"><strong>Afdeling:</strong> {{ $profiel->afdeling ?? '-' }}</p>
                    <p class="mb-3"><strong>Telefoon:</strong> {{ $profiel->telefoon ?? '-' }}</p>
                    <a class="btn btn-outline-success" href="{{ route('profile.edit') }}">Profiel aanpassen</a>
                </div>
            </div>
        </div>
    </div>
@endsection
