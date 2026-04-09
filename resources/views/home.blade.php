@extends('layouts.app')

@section('title', 'Home | Voedselbank Maaskantje')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="h3">Home</h1>
            <p class="text-secondary mb-3">
                Welkom bij Voedselbank Maaskantje. Wij ondersteunen gezinnen met een voedselpakket,
                werken samen met lokale partners en zorgen voor een eerlijke verdeling van donaties.
            </p>

            @guest
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-success" href="{{ route('login') }}">Inloggen</a>
                    <a class="btn btn-outline-success" href="{{ route('register.form') }}">Registreren</a>
                </div>
            @else
                <a class="btn btn-success" href="{{ route('dashboard.redirect') }}">Naar mijn dashboard</a>
            @endguest
        </div>
    </div>
@endsection
