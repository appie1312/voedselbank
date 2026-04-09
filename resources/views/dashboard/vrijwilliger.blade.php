@extends('layouts.app')

@section('title', 'Vrijwilliger Dashboard')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-1">Vrijwilliger Dashboard</h1>
        <p class="text-secondary mb-0">Jouw planning en taken voor hulp aan cliënten.</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h5">Planning</h2>
                    <ul class="mb-0">
                        <li>Donderdag: uitgiftebalie 09:00 - 12:00</li>
                        <li>Vrijdag: pakket sorteren 13:00 - 16:00</li>
                        <li>Zaterdag: huis-aan-huis levering</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h2 class="h5">Mijn profiel</h2>
                    <p class="mb-1"><strong>Naam:</strong> {{ $profiel->name ?? auth()->user()->name }}</p>
                    <p class="mb-1"><strong>Beschikbaarheid:</strong> {{ $profiel->beschikbaarheid ?? '-' }}</p>
                    <p class="mb-3"><strong>Telefoon:</strong> {{ $profiel->telefoon ?? '-' }}</p>
                    <a class="btn btn-outline-success" href="{{ route('profile.edit') }}">Profiel aanpassen</a>
                </div>
            </div>
        </div>
    </div>
@endsection
