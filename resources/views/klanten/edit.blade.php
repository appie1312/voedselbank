@extends('layouts.app')

@section('title', 'Wijzig Klant')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Wijzig klant</h1>
                <a href="{{ route('klanten.index') }}" class="btn btn-outline-secondary">Terug naar overzicht</a>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <strong>Totaal voedselpakketten:</strong> {{ (int) ($klant->totaal_voedselpakketten ?? 0) }}
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <strong>Wensen/allergieën:</strong> {{ $klant->wensen_allergieen ?: '-' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('klanten.update', ['klantId' => $klant->id]) }}" class="row g-3 needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="col-12 col-lg-6">
                    <label for="gezinsnaam" class="form-label">Gezinsnaam</label>
                    <input
                        id="gezinsnaam"
                        name="gezinsnaam"
                        type="text"
                        class="form-control"
                        maxlength="100"
                        pattern="[A-Za-z0-9 .,'()\-]*"
                        value="{{ old('gezinsnaam', $klant->gezinsnaam) }}"
                        required
                    >
                    <div class="invalid-feedback">Gezinsnaam is verplicht en bevat alleen geldige tekens.</div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="adres" class="form-label">Adres</label>
                    <input
                        id="adres"
                        name="adres"
                        type="text"
                        class="form-control"
                        maxlength="255"
                        value="{{ old('adres', $klant->adres) }}"
                        required
                    >
                    <div class="invalid-feedback">Adres is verplicht.</div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label for="telefoonnummer" class="form-label">Telefoonnummer</label>
                    <input
                        id="telefoonnummer"
                        name="telefoonnummer"
                        type="text"
                        class="form-control"
                        maxlength="20"
                        pattern="[0-9+\-\s()]*"
                        value="{{ old('telefoonnummer', $klant->telefoonnummer) }}"
                        required
                    >
                    <div class="invalid-feedback">Telefoonnummer is verplicht en bevat alleen cijfers/tekens.</div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <label for="emailadres" class="form-label">E-mailadres</label>
                    <input
                        id="emailadres"
                        name="emailadres"
                        type="email"
                        class="form-control"
                        maxlength="150"
                        value="{{ old('emailadres', $klant->emailadres) }}"
                    >
                    <div class="invalid-feedback">Vul een geldig e-mailadres in.</div>
                </div>

                <div class="col-12 col-md-4 col-lg-4">
                    <label for="aantal_volwassenen" class="form-label">Aantal Volwassen</label>
                    <input
                        id="aantal_volwassenen"
                        name="aantal_volwassenen"
                        type="number"
                        class="form-control"
                        min="0"
                        max="20"
                        value="{{ old('aantal_volwassenen', $klant->aantal_volwassenen) }}"
                        required
                    >
                    <div class="invalid-feedback">Vul een getal tussen 0 en 20 in.</div>
                </div>

                <div class="col-12 col-md-4 col-lg-4">
                    <label for="aantal_kinderen" class="form-label">Aantal Kinderen</label>
                    <input
                        id="aantal_kinderen"
                        name="aantal_kinderen"
                        type="number"
                        class="form-control"
                        min="0"
                        max="20"
                        value="{{ old('aantal_kinderen', $klant->aantal_kinderen) }}"
                        required
                    >
                    <div class="invalid-feedback">Vul een getal tussen 0 en 20 in.</div>
                </div>

                <div class="col-12 col-md-4 col-lg-4">
                    <label for="aantal_babys" class="form-label">Aantal Baby</label>
                    <input
                        id="aantal_babys"
                        name="aantal_babys"
                        type="number"
                        class="form-control"
                        min="0"
                        max="20"
                        value="{{ old('aantal_babys', $klant->aantal_babys) }}"
                        required
                    >
                    <div class="invalid-feedback">Vul een getal tussen 0 en 20 in.</div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-warning fw-semibold">Update</button>
                    <a href="{{ route('klanten.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                </div>
            </form>
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
