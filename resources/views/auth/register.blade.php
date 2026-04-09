@extends('layouts.app')

@section('title', 'Registreren | Voedselbank Maaskantje')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-9 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-2">Registreren</h1>
                    <p class="text-secondary mb-4">Maak een account aan. Nieuwe accounts worden automatisch vrijwilliger.</p>

                    <form method="POST" action="{{ route('register') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label for="name" class="form-label">Naam</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="password_confirmation" class="form-label">Bevestig wachtwoord</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">Registreren</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
