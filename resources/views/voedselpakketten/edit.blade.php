@extends('layouts.app')

@section('title', 'Voedselpakket Wijzigen')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h4 mb-0">Pakket Wijzigen #{{ $pakket->id }}</h1>
                    <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary btn-sm">Terug</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        Controleer het formulier en probeer het opnieuw.
                    </div>
                @endif

                <form action="{{ route(auth()->user()->role . '.voedselpakketten.update', $pakket->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="klant_id" class="form-label">Gezin</label>
                        <select name="klant_id" id="klant_id" class="form-select @error('klant_id') is-invalid @enderror" required>
                            @foreach($klanten as $klant)
                                <option value="{{ $klant->id }}" @selected(old('klant_id', $pakket->klant_id) == $klant->id)>
                                    {{ $klant->gezinsnaam }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Kies een geldig gezin.</div>
                    </div>

                    <div class="form-check mb-4">
                        {{-- Alleen statusbeheer: inhoud bewerk je via Samenstellen. --}}
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="opgehaald"
                            name="opgehaald"
                            value="1"
                            @checked(old('opgehaald', !is_null($pakket->datum_uitgifte)))
                        >
                        <label class="form-check-label" for="opgehaald">
                            Pakket is opgehaald
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                        <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var forms = document.querySelectorAll('.needs-validation');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection
