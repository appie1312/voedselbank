@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Nieuwe Leverancier</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('leveranciers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="naam" class="form-label">Naam *</label>
            <input type="text" class="form-control @error('naam') is-invalid @enderror" id="naam" name="naam" value="{{ old('naam') }}" required>
            @error('naam')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="adres" class="form-label">Adres</label>
            <input type="text" class="form-control @error('adres') is-invalid @enderror" id="adres" name="adres" value="{{ old('adres') }}">
            @error('adres')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="telefoon" class="form-label">Telefoon</label>
            <input type="text" class="form-control @error('telefoon') is-invalid @enderror" id="telefoon" name="telefoon" value="{{ old('telefoon') }}">
            @error('telefoon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="{{ route('leveranciers.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection