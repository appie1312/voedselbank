@extends('layouts.app')

@php
    $isEdit = isset($leverancier) && $leverancier;
@endphp

@section('title', $isEdit ? 'Leverancier Wijzigen' : 'Leverancier Toevoegen')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h1 class="h4 mb-0">{{ $isEdit ? 'Leverancier wijzigen' : 'Nieuwe leverancier' }}</h1>
            <a href="{{ route('leveranciers.index') }}" class="btn btn-outline-secondary">Terug naar overzicht</a>
        </div>

        @if($errors->has('bedrijfsnaam'))
            <div class="alert alert-danger">{{ $errors->first('bedrijfsnaam') }}</div>
        @endif

        <form method="POST" action="{{ $isEdit ? route('leveranciers.update', $leverancier->id) : route('leveranciers.store') }}" class="row g-3">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="col-12 col-lg-6">
                <label for="bedrijfsnaam" class="form-label">Bedrijfsnaam</label>
                <input type="text" class="form-control" id="bedrijfsnaam" name="bedrijfsnaam" value="{{ old('bedrijfsnaam', $leverancier->bedrijfsnaam ?? '') }}" maxlength="150" required>
            </div>

            <div class="col-12 col-lg-6">
                <label for="telefoonnummer" class="form-label">Telefoonnummer</label>
                <input type="text" class="form-control" id="telefoonnummer" name="telefoonnummer" value="{{ old('telefoonnummer', $leverancier->telefoonnummer ?? '') }}" maxlength="20" required>
            </div>

            <div class="col-12">
                <label for="adres" class="form-label">Adres</label>
                <input type="text" class="form-control" id="adres" name="adres" value="{{ old('adres', $leverancier->adres ?? '') }}" maxlength="255" required>
            </div>

            <div class="col-12 col-lg-6">
                <label for="contactpersoon_naam" class="form-label">Contactpersoon</label>
                <input type="text" class="form-control" id="contactpersoon_naam" name="contactpersoon_naam" value="{{ old('contactpersoon_naam', $leverancier->contactpersoon_naam ?? '') }}" maxlength="100" required>
            </div>

            <div class="col-12 col-lg-6">
                <label for="contactpersoon_email" class="form-label">E-mail contactpersoon</label>
                <input type="email" class="form-control" id="contactpersoon_email" name="contactpersoon_email" value="{{ old('contactpersoon_email', $leverancier->contactpersoon_email ?? '') }}" maxlength="150">
            </div>

            <div class="col-12 col-lg-6">
                <label for="volgende_levering" class="form-label">Volgende levering</label>
                <input type="date" class="form-control" id="volgende_levering" name="volgende_levering" value="{{ old('volgende_levering', isset($leverancier?->volgende_levering) ? \Carbon\Carbon::parse($leverancier->volgende_levering)->format('Y-m-d') : '') }}">
            </div>

            <div class="col-12">
                <label class="form-label d-block">Producten die deze leverancier levert</label>
                <div class="border rounded p-3 bg-light">
                    @forelse($producten as $product)
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="product_ids[]"
                                value="{{ $product->id }}"
                                id="product_{{ $product->id }}"
                                {{ in_array($product->id, old('product_ids', $geselecteerdeProductIds ?? [])) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="product_{{ $product->id }}">
                                {{ $product->productnaam }}
                            </label>
                        </div>
                    @empty
                        <p class="mb-0 text-muted">Nog geen producten beschikbaar om te koppelen.</p>
                    @endforelse
                </div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-success">OK</button>
                <a href="{{ route('leveranciers.index') }}" class="btn btn-outline-secondary">Annuleren</a>
            </div>
        </form>
    </div>
</div>
@endsection
