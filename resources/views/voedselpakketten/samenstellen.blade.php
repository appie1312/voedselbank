@extends('layouts.app')

@section('title', 'Pakket Samenstellen')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Pakket Samenstellen #{{ $pakket->id }}</h1>
            <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary btn-sm">Terug</a>
        </div>

        <p class="text-muted mb-4">Gezin: <strong>{{ $pakket->gezinsnaam }}</strong></p>

        <form method="POST" action="{{ route(auth()->user()->role . '.voedselpakketten.samenstellen.opslaan', $pakket->id) }}">
            @csrf

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>EAN</th>
                            <th>Beschikbaar</th>
                            <th style="width: 140px;">Aantal in pakket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $huidigeCategorie = null; @endphp
                        @forelse($voorraadProducten as $product)
                            @if ($huidigeCategorie !== $product->categorie_naam)
                                @php $huidigeCategorie = $product->categorie_naam; @endphp
                                {{-- Visuele scheiding per categorie voor sneller samenstellen. --}}
                                <tr class="table-secondary">
                                    <td colspan="4" class="fw-bold text-uppercase small py-2">
                                        Categorie: {{ $huidigeCategorie }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td>{{ $product->productnaam }}</td>
                                <td>{{ $product->ean_nummer }}</td>
                                <td>{{ (int) $product->hoeveelheid }}</td>
                                <td>
                                    <input
                                        type="number"
                                        min="0"
                                        class="form-control"
                                        name="aantallen[{{ $product->id }}]"
                                        value="{{ old('aantallen.' . $product->id, (int) ($huidigeSamenstelling[$product->id] ?? 0)) }}"
                                    >
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Geen producten in voorraad gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-success">Samenstelling opslaan</button>
        </form>
    </div>
</div>
@endsection
