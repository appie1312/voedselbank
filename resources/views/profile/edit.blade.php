@extends('layouts.app')

@section('title', 'Mijn Profiel')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-2">Mijn profiel</h1>
                    <p class="text-secondary">Pas je eigen gegevens aan.</p>

                    <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')

                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label">Naam</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $profiel->name ?? auth()->user()->name) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $profiel->email ?? auth()->user()->email) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="telefoon" class="form-label">Telefoon</label>
                            <input id="telefoon" name="telefoon" type="text" class="form-control" value="{{ old('telefoon', $profiel->telefoon ?? '') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="adres" class="form-label">Adres</label>
                            <input id="adres" name="adres" type="text" class="form-control" value="{{ old('adres', $profiel->adres ?? '') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="afdeling" class="form-label">Afdeling</label>
                            <input id="afdeling" name="afdeling" type="text" class="form-control" value="{{ old('afdeling', $profiel->afdeling ?? '') }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="beschikbaarheid" class="form-label">Beschikbaarheid</label>
                            <input id="beschikbaarheid" name="beschikbaarheid" type="text" class="form-control" value="{{ old('beschikbaarheid', $profiel->beschikbaarheid ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label for="verantwoordelijkheden" class="form-label">Verantwoordelijkheden</label>
                            <input id="verantwoordelijkheden" name="verantwoordelijkheden" type="text" class="form-control" value="{{ old('verantwoordelijkheden', $profiel->verantwoordelijkheden ?? '') }}">
                        </div>

                        <div class="col-12">
                            <label for="bio" class="form-label">Korte profieltekst</label>
                            <textarea id="bio" name="bio" rows="4" class="form-control">{{ old('bio', $profiel->bio ?? '') }}</textarea>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">Profiel opslaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
