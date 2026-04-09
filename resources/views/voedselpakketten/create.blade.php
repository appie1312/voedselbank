@extends('layouts.app')

@section('title', 'Nieuw Voedselpakket')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h4 mb-0">Nieuw Voedselpakket Registreren</h1>
                    <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary btn-sm">Terug</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        Controleer het formulier en probeer het opnieuw.
                    </div>
                @endif

                <form action="{{ route(auth()->user()->role . '.voedselpakketten.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    {{-- Registratie is bewust simpel: eerst gezin kiezen, daarna samenstellen. --}}
                    <div class="mb-3">
                        <label for="klant_id" class="form-label">Gezin</label>
                        <select name="klant_id" id="klant_id" class="form-select @error('klant_id') is-invalid @enderror" required>
                            <option value="">-- Kies een gezin --</option>
                            @foreach($klanten as $klant)
                                <option value="{{ $klant->id }}" @selected(old('klant_id') == $klant->id)>{{ $klant->gezinsnaam }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Kies een gezin voor dit pakket.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Registreren en inhoud toevoegen</button>
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
