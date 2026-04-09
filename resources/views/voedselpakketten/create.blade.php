@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nieuw Voedselpakket Registreren</h2>

    <form action="{{ route('voedselpakketten.store') }}" method="POST">
        @csrf <div class="mb-3">
            <label for="klant_id" class="form-label">Voor welk gezin (Klant) is dit pakket?</label>
            <select name="klant_id" id="klant_id" class="form-select" required>
                <option value="">-- Kies een gezin --</option>
                @foreach($klanten as $klant)
                    <option value="{{ $klant->id }}">{{ $klant->gezinsnaam }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="opmerking" class="form-label">Opmerkingen (optioneel)</label>
            <textarea name="opmerking" id="opmerking" class="form-control" rows="3" placeholder="Bijv: Klant komt later ophalen ivm werk..."></textarea>
        </div>

        <button type="submit" class="btn btn-success">Pakket Aanmaken</button>
        <a href="{{ route('voedselpakketten.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection