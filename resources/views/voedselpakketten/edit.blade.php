@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pakket Wijzigen (ID: {{ $pakket->id }})</h2>

    <form action="{{ route('voedselpakketten.update', $pakket->id) }}" method="POST">
        @csrf
        @method('PUT') <div class="mb-3">
            <label for="klant_id" class="form-label">Gezin (Klant)</label>
            <select name="klant_id" id="klant_id" class="form-select" required>
                @foreach($klanten as $klant)
                    <option value="{{ $klant->id }}" {{ $pakket->klant_id == $klant->id ? 'selected' : '' }}>
                        {{ $klant->gezinsnaam }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="opgehaald" name="opgehaald" value="1" 
                   {{ !is_null($pakket->datum_uitgifte) ? 'checked' : '' }}>
            <label class="form-check-label font-weight-bold text-success" for="opgehaald">
                Pakket is succesvol opgehaald door de klant
            </label>
        </div>

        <div class="mb-3">
            <label for="opmerking" class="form-label">Opmerkingen</label>
            <textarea name="opmerking" id="opmerking" class="form-control" rows="3">{{ $pakket->opmerking }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Wijzigingen Opslaan</button>
        <a href="{{ route('voedselpakketten.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection