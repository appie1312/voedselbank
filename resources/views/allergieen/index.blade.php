@extends('layouts.app')

@section('title', 'Overzicht Allergieen')

@section('content')
    @php
        $klantId = $filters['klant_id'] ?? null;
        $zoekterm = (string) ($filters['zoekterm'] ?? '');
        $aantalRijen = (int) ($filters['aantal_rijen'] ?? 10);
        $vasteRijen = 5;
        $legeRijen = max(0, $vasteRijen - $allergieen->count());
    @endphp

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Allergieen</h1>
                <a href="{{ route('klanten.index') }}" class="btn btn-outline-secondary">Terug naar klanten</a>
            </div>

            <form method="POST" action="{{ route('allergieen.store') }}" class="row g-3 needs-validation mb-4" novalidate>
                @csrf

                <div class="col-12 col-lg-3">
                    <label for="nieuwe_klant_id" class="form-label">Klant ID</label>
                    <input
                        id="nieuwe_klant_id"
                        name="klant_id"
                        type="number"
                        class="form-control"
                        min="1"
                        value="{{ old('klant_id', $klantId) }}"
                        placeholder="Bijv. 3"
                        required
                    >
                    <div class="invalid-feedback">Klant ID is verplicht.</div>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="beschrijving" class="form-label">Nieuwe allergie</label>
                    <input
                        id="beschrijving"
                        name="beschrijving"
                        type="text"
                        class="form-control"
                        maxlength="100"
                        pattern="[A-Za-z0-9 .,'()\-]*"
                        value="{{ old('beschrijving') }}"
                        placeholder="Bijv. Noten"
                        required
                    >
                    <div class="invalid-feedback">Vul een geldige beschrijving in.</div>
                </div>

                <div class="col-12 col-lg-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Toevoegen</button>
                </div>
            </form>

            <form method="GET" action="{{ route('allergieen.index') }}" class="row g-3 needs-validation" novalidate>
                <div class="col-12 col-md-3">
                    <label for="klant_id" class="form-label">Klant ID</label>
                    <input
                        id="klant_id"
                        name="klant_id"
                        type="number"
                        class="form-control"
                        min="1"
                        value="{{ $klantId }}"
                        placeholder="Leeg = alle klanten"
                    >
                    <div class="invalid-feedback">Vul een geldig klant-id in.</div>
                </div>

                <div class="col-12 col-md-4">
                    <label for="zoekterm" class="form-label">Beschrijving / Gezinsnaam</label>
                    <input
                        id="zoekterm"
                        name="zoekterm"
                        type="text"
                        class="form-control"
                        maxlength="100"
                        pattern="[A-Za-z0-9 .,'()\-]*"
                        value="{{ $zoekterm }}"
                        placeholder="Zoekterm"
                    >
                    <div class="invalid-feedback">Gebruik alleen letters, cijfers en standaard leestekens.</div>
                </div>

                <div class="col-12 col-md-2">
                    <label for="aantal_rijen" class="form-label">Aantal</label>
                    <input
                        id="aantal_rijen"
                        name="aantal_rijen"
                        type="number"
                        class="form-control"
                        min="1"
                        max="50"
                        required
                        value="{{ $aantalRijen }}"
                    >
                    <div class="invalid-feedback">Vul een getal tussen 1 en 50 in.</div>
                </div>

                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('allergieen.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if (! isset($status_error) && $allergieen->isEmpty())
                <div class="alert alert-warning" role="alert">
                    Er zijn geen allergieen beschikbaar.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Klant ID</th>
                            <th>Gezinsnaam</th>
                            <th>Allergie ID</th>
                            <th>Beschrijving</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($status_error))
                            <tr>
                                <td colspan="5">Door een storing kunnen allergieen nu niet worden weergegeven.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endfor
                        @elseif ($allergieen->isEmpty())
                            @for ($index = 0; $index < $vasteRijen; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endfor
                        @else
                            @php
                                $verwerkteAllergieIds = [];
                            @endphp
                            @foreach ($allergieen as $allergie)
                                @php
                                    $allergieId = (int) $allergie->allergie_id;
                                    $toonVerwijderKnop = ! in_array($allergieId, $verwerkteAllergieIds, true);
                                    if ($toonVerwijderKnop) {
                                        $verwerkteAllergieIds[] = $allergieId;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ isset($allergie->klant_id) ? (int) $allergie->klant_id : '-' }}</td>
                                    <td>{{ $allergie->gezinsnaam ?? '-' }}</td>
                                    <td>{{ $allergieId }}</td>
                                    <td>{{ $allergie->allergie_beschrijving }}</td>
                                    <td>
                                        @if ($toonVerwijderKnop)
                                            <form method="POST" action="{{ route('allergieen.destroy', ['allergieId' => $allergieId]) }}" onsubmit="return confirm('Weet je zeker dat je deze allergie wilt verwijderen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm fw-semibold">
                                                    Verwijder Allergie
                                                </button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @for ($index = 0; $index < $legeRijen; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
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
