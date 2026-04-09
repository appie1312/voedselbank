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
                    <label for="zoekterm" class="form-label">Beschrijving</label>
                    <input
                        id="zoekterm"
                        name="zoekterm"
                        type="text"
                        class="form-control"
                        maxlength="100"
                        pattern="[A-Za-z0-9 .,'()\\-]*"
                        value="{{ $zoekterm }}"
                        placeholder="Zoek op allergie of gezinsnaam"
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
                    <button type="submit" class="btn btn-success">Toon</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($status_error))
                            <tr>
                                <td colspan="4">Door een storing kunnen allergieen nu niet worden weergegeven.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
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
                                </tr>
                            @endfor
                        @else
                            @foreach ($allergieen as $allergie)
                                <tr>
                                    <td>{{ (int) ($allergie->klant_id ?? 0) }}</td>
                                    <td>{{ $allergie->gezinsnaam ?? '-' }}</td>
                                    <td>{{ (int) $allergie->allergie_id }}</td>
                                    <td>{{ $allergie->allergie_beschrijving }}</td>
                                </tr>
                            @endforeach

                            @for ($index = 0; $index < $legeRijen; $index++)
                                <tr class="table-light">
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
