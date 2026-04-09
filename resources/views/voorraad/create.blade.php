@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Product toevoegen aan voorraad</h1>
                <a href="{{ route('voorraad') }}" class="btn btn-outline-secondary">Terug naar overzicht</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Controleer je invoer:</strong> {{ $errors->first() }}
                </div>
            @endif

            @if (count($productenNietInVoorraad) === 0)
                <div class="alert alert-info mb-0" role="alert">
                    Alle producten staan al in de voorraad.
                </div>
            @else
                <form method="POST" action="{{ route('voorraad.store') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label for="product_naam" class="form-label">Product</label>
                        <input
                            type="text"
                            id="product_naam"
                            name="product_naam"
                            class="form-control"
                            list="beschikbare-producten"
                            value="{{ old('product_naam') }}"
                            placeholder="Typ een productnaam"
                            required
                        >
                        <small class="text-muted">Bestaat de productnaam nog niet, dan wordt het product automatisch aangemaakt.</small>
                        <datalist id="beschikbare-producten">
                            @foreach ($productenNietInVoorraad as $product)
                                <option value="{{ $product->productnaam }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label for="hoeveelheid" class="form-label">Aantal</label>
                        <input type="number" min="0" id="hoeveelheid" name="hoeveelheid" class="form-control" value="{{ old('hoeveelheid', 0) }}" required>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label for="minimum_voorraad" class="form-label">Minimumvoorraad</label>
                        <input type="number" min="0" id="minimum_voorraad" name="minimum_voorraad" class="form-control" value="{{ old('minimum_voorraad', 0) }}" required>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label for="locatie" class="form-label">Locatie</label>
                        <select id="locatie" name="locatie" class="form-select">
                            <option value="">Kies een locatie</option>
                            @foreach ($locaties as $locatie)
                                <option value="{{ $locatie }}" {{ old('locatie') === $locatie ? 'selected' : '' }}>
                                    {{ $locatie }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success">Opslaan</button>
                        <a href="{{ route('voorraad') }}" class="btn btn-outline-secondary">Annuleren</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
