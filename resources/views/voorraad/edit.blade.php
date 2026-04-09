@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Voorraadregel wijzigen</h1>
                <a href="{{ route('voorraad') }}" class="btn btn-outline-secondary">Terug naar overzicht</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Controleer je invoer:</strong> {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-3">
                <div><strong>Product:</strong> {{ $voorraadItem->product_naam }}</div>
                <div><strong>Categorie:</strong> {{ $voorraadItem->categorie_naam }}</div>
            </div>

            <form method="POST" action="{{ route('voorraad.update', $voorraadItem->product_id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12 col-lg-4">
                    <label for="hoeveelheid" class="form-label">Aantal</label>
                    <input
                        type="number"
                        min="0"
                        id="hoeveelheid"
                        name="hoeveelheid"
                        class="form-control"
                        value="{{ old('hoeveelheid', $voorraadItem->hoeveelheid) }}"
                        required
                    >
                </div>

                <div class="col-12 col-lg-4">
                    <label for="minimum_voorraad" class="form-label">Minimumvoorraad</label>
                    <input
                        type="number"
                        min="0"
                        id="minimum_voorraad"
                        name="minimum_voorraad"
                        class="form-control"
                        value="{{ old('minimum_voorraad', $voorraadItem->minimum_voorraad) }}"
                        required
                    >
                </div>

                <div class="col-12 col-lg-4">
                    <label for="locatie" class="form-label">Locatie</label>
                    <select id="locatie" name="locatie" class="form-select">
                        <option value="">Kies een locatie</option>
                        @foreach ($locaties as $locatie)
                            <option value="{{ $locatie }}" {{ old('locatie', $voorraadItem->locatie) === $locatie ? 'selected' : '' }}>
                                {{ $locatie }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                    <a href="{{ route('voorraad') }}" class="btn btn-outline-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
