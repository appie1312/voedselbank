@extends('layouts.app')

@section('title', 'Inloggen | Voedselbank Maaskantje')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h4 mb-2">Inloggen</h1>
                    <p class="text-secondary mb-4">Log in met je e-mailadres en wachtwoord.</p>

                    <form method="POST" action="{{ route('login.attempt') }}" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label for="email" class="form-label">E-mailadres</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label">Wachtwoord</label>
                            <input id="password" name="password" type="password" class="form-control" required>
                        </div>

                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-success">Inloggen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
