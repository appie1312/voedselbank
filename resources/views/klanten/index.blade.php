@extends('layouts.app')

@section('title', 'Overzicht Klanten')

@section('content')
    @php
        $zoekterm = (string) ($filters['zoekterm'] ?? '');
        $aantalRijen = (int) ($filters['aantal_rijen'] ?? 5);
        $vasteRijen = 5;
        $legeRijen = max(0, $vasteRijen - $klanten->count());
    @endphp

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h1 class="h4 mb-0">Overzicht Klanten</h1>
                <a href="{{ route('klanten.create') }}" class="btn btn-success">Klant toevoegen</a>
            </div>

            <form method="GET" action="{{ route('klanten.index') }}" class="row g-3 needs-validation" novalidate>
                <div class="col-12 col-lg-6">
                    <label for="zoekterm" class="form-label">Zoekterm</label>
                    <input
                        id="zoekterm"
                        name="zoekterm"
                        type="text"
                        class="form-control"
                        value="{{ $zoekterm }}"
                        maxlength="150"
                        pattern="[A-Za-z0-9 .,'@()\-]*"
                        placeholder="Zoek op gezinsnaam, adres, e-mailadres of telefoon"
                    >
                    <div class="invalid-feedback">Gebruik alleen letters, cijfers en standaard leestekens.</div>
                </div>

                <div class="col-12 col-md-4 col-lg-3">
                    <label for="aantal_rijen" class="form-label">Aantal rijen</label>
                    <input
                        id="aantal_rijen"
                        name="aantal_rijen"
                        type="number"
                        class="form-control"
                        min="1"
                        max="25"
                        required
                        value="{{ $aantalRijen }}"
                    >
                    <div class="invalid-feedback">Vul een getal tussen 1 en 25 in.</div>
                </div>

                <div class="col-12 col-md-8 col-lg-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success">Filteren</button>
                    <a href="{{ route('klanten.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Gezinsnaam</th>
                            <th>Adres</th>
                            <th>Telefoonnummer</th>
                            <th>E-mailadres</th>
                            <th>Aantal Volwassen</th>
                            <th>Aantal Kinderen</th>
                            <th>Aantal Baby</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($status_error))
                            <tr>
                                <td colspan="7">Door een storing kunnen klanten nu niet worden weergegeven.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                            @endfor
                        @elseif ($klanten->isEmpty())
                            <tr>
                                <td colspan="7">Geen klanten gevonden.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                </tr>
                            @endfor
                        @else
                            @foreach ($klanten as $klant)
                                <tr>
                                    <td>{{ $klant->gezinsnaam }}</td>
                                    <td>{{ $klant->adres }}</td>
                                    <td>{{ $klant->telefoonnummer }}</td>
                                    <td>{{ $klant->emailadres ?: '-' }}</td>
                                    <td>{{ (int) $klant->aantal_volwassenen }}</td>
                                    <td>{{ (int) $klant->aantal_kinderen }}</td>
                                    <td>{{ (int) $klant->aantal_babys }}</td>
                                </tr>
                            @endforeach

                            @for ($index = 0; $index < $legeRijen; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
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
