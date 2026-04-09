@extends('layouts.app')

@section('title', 'Overzicht Voedselpakketten')

@section('content')
    @php
        // We gebruiken request() om errors te voorkomen als er niet gefilterd wordt
        $zoekterm = request('zoekterm', '');
        $aantalRijen = (int) request('aantal_rijen', 5);
        $vasteRijen = 5;
        // Zorg dat we niet onder de 0 uitkomen met lege rijen
        $legeRijen = max(0, $vasteRijen - (isset($pakketten) ? $pakketten->count() : 0));
    @endphp

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Overzicht Voedselpakketten</h1>
                <a href="{{ route(auth()->user()->role . '.voedselpakketten.create') }}" class="btn btn-success">Nieuw Pakket Registreren</a>            
            </div>

            <form method="GET" action="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="row g-3 needs-validation" novalidate>
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
                        placeholder="Zoek op ID of gezinsnaam..."
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
                    <button type="submit" class="btn btn-primary">Filteren</button>
                    <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <th>Pakket ID</th>
                            <th>Gezinsnaam</th>
                            <th>Inhoud</th>
                            <th>Samengesteld op</th>
                            <th>Status</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($status_error))
                            <tr>
                                <td colspan="6">Door een storing kunnen pakketten nu niet worden weergegeven.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                </tr>
                            @endfor
                        @elseif (!isset($pakketten) || $pakketten->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">Er zijn geen voedselpakketten gevonden.</td>
                            </tr>
                            @for ($index = 0; $index < $vasteRijen - 1; $index++)
                                <tr class="table-light">
                                    <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
                                </tr>
                            @endfor
                        @else
                            @foreach ($pakketten as $pakket)
                                <tr>
                                    <td class="fw-bold">#{{ $pakket->id }}</td>
                                    <td>{{ $pakket->gezinsnaam ?? 'Geen naam' }}</td>
                                    <td>
                                        {{-- Inhoud compact tonen via modal zodat de tabel leesbaar blijft. --}}
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#inhoudModal{{ $pakket->id }}"
                                        >
                                            Info
                                        </button>

                                        <div class="modal fade" id="inhoudModal{{ $pakket->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Inhoud pakket #{{ $pakket->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if (!empty($pakket->inhoud_tekst) && $pakket->inhoud_tekst !== '-')
                                                            <p class="mb-0">{{ $pakket->inhoud_tekst }}</p>
                                                        @else
                                                            <p class="mb-0 text-muted">Dit pakket heeft nog geen inhoud.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pakket->datum_samenstelling)->format('d-m-Y') }}</td>
                                    <td>
                                        @if(is_null($pakket->datum_uitgifte))
                                            <span class="badge bg-warning text-dark">Klaar voor uitgifte</span>
                                        @else
                                            <span class="badge bg-success">Opgehaald: {{ \Carbon\Carbon::parse($pakket->datum_uitgifte)->format('d-m-Y') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route(auth()->user()->role . '.voedselpakketten.samenstellen', $pakket->id) }}" class="btn btn-sm btn-info text-white">Samenstellen</a>
                                        
                                        <a href="{{ route(auth()->user()->role . '.voedselpakketten.edit', $pakket->id) }}" class="btn btn-sm btn-warning">Wijzig</a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verwijderModal{{ $pakket->id }}"
                                        >
                                            Verwijder
                                        </button>

                                        <div class="modal fade" id="verwijderModal{{ $pakket->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Voedselpakket verwijderen</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if (is_null($pakket->datum_uitgifte))
                                                            {{-- UI-hint; echte blokkade zit ook server-side. --}}
                                                            Dit pakket kan niet verwijderd worden, want het is nog niet opgehaald.
                                                        @else
                                                            Weet je zeker dat je pakket <strong>#{{ $pakket->id }}</strong> wilt verwijderen?
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Sluiten</button>
                                                        @if (!is_null($pakket->datum_uitgifte))
                                                            <form action="{{ route(auth()->user()->role . '.voedselpakketten.destroy', $pakket->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Ja, verwijder</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
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
