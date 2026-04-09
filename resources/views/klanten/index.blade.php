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
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('allergieen.index') }}" class="btn btn-outline-primary">Allergieen overzicht</a>
                    <a href="{{ route('klanten.create') }}" class="btn btn-success">Klant toevoegen</a>
                </div>
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
                            <th>Status</th>
                            <th>Aantal Volwassen</th>
                            <th>Aantal Kinderen</th>
                            <th>Aantal Baby</th>
                            <th>Allergie</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($status_error))
                            <tr>
                                <td colspan="10">Door een storing kunnen klanten nu niet worden weergegeven.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endfor
                        @elseif ($klanten->isEmpty())
                            <tr>
                                <td colspan="10">Geen klanten gevonden.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            @endfor
                        @else
                            @foreach ($klanten as $klant)
                                @php
                                    $statusWaarde = (string) ($klant->aanwezigheidsstatus ?? '');
                                    $kanVerwijderen = in_array($statusWaarde, ['afwezig', 'buiten_land'], true);
                                    $isAanwezig = $statusWaarde === 'binnen_land';
                                    $aanwezigLabel = $isAanwezig ? 'Aanwezig' : ($kanVerwijderen ? 'Niet aanwezig' : 'Onbekend');
                                    $statusDetail = match ($statusWaarde) {
                                        'binnen_land' => 'Binnen land',
                                        'buiten_land' => 'Buiten land',
                                        'afwezig' => 'Afwezig',
                                        default => 'Status onbekend',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $klant->gezinsnaam }}</td>
                                    <td>{{ $klant->adres }}</td>
                                    <td>{{ $klant->telefoonnummer }}</td>
                                    <td>{{ $klant->emailadres ?: '-' }}</td>
                                    <td>
                                        <div class="status-wrap">
                                            <span class="status-pill {{ $isAanwezig ? 'status-pill--present' : 'status-pill--away' }}">
                                                {{ $aanwezigLabel }}
                                            </span>
                                            <div class="status-subtext">{{ $statusDetail }}</div>
                                        </div>
                                    </td>
                                    <td>{{ (int) $klant->aantal_volwassenen }}</td>
                                    <td>{{ (int) $klant->aantal_kinderen }}</td>
                                    <td>{{ (int) $klant->aantal_babys }}</td>
                                    <td>
                                        <a href="{{ route('allergieen.index', ['klant_id' => $klant->id]) }}" class="btn btn-info btn-sm fw-semibold text-white">
                                            Allergie
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 flex-wrap action-group">
                                            <a href="{{ route('klanten.edit', ['klantId' => $klant->id]) }}" class="btn btn-warning btn-sm fw-semibold action-btn">
                                                Wijzig
                                            </a>
                                            <form method="POST" action="{{ route('klanten.destroy', ['klantId' => $klant->id]) }}" class="m-0" onsubmit="return confirm('Weet je zeker dat je deze klant wilt verwijderen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm fw-semibold action-btn">
                                                    Verwijder
                                                </button>
                                            </form>
                                        </div>
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
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
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

    <style>
        .action-group {
            min-width: 13.5rem;
        }

        .action-btn {
            min-width: 6.2rem;
        }

        .status-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.18rem;
            min-width: 7.2rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
            padding: 0.3rem 0.58rem;
            border: 1px solid transparent;
        }

        .status-pill--present {
            background-color: #fff3cd;
            border-color: #ffe69c;
            color: #664d03;
        }

        .status-pill--away {
            background-color: #d1f4e0;
            border-color: #9fd8be;
            color: #0f5132;
        }

        .status-subtext {
            color: #6c757d;
            font-size: 0.72rem;
            line-height: 1.1;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
        }

        @media (max-width: 1199px) {
            .action-group {
                min-width: 11.5rem;
            }
        }
    </style>
@endsection
