@extends('layouts.app')

@section('title', 'Wijzig Status Voedselpakket')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h4 mb-0">Wijzig Status Voedselpakket #{{ $pakket->id }}</h1>
                    <a href="{{ route(auth()->user()->role . '.voedselpakketten.index') }}" class="btn btn-outline-secondary btn-sm">Terug</a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        Controleer het formulier en probeer het opnieuw.
                    </div>
                @endif

                @if ($statusWijzigingGeblokkeerd ?? false)
                    <div class="alert alert-warning" role="alert">
                        {{ $statusNietMogelijkMelding }}
                    </div>
                @endif

                <div class="mb-3">
                    <strong>Gezin:</strong> {{ $pakket->gezinsnaam }}
                </div>
                <div class="mb-4">
                    <strong>Huidige status:</strong>
                    @if (is_null($pakket->datum_uitgifte))
                        Niet Uitgereikt
                    @else
                        Uitgereikt
                    @endif
                </div>

                <form action="{{ route(auth()->user()->role . '.voedselpakketten.update', $pakket->id) }}" method="POST" class="needs-validation">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            @disabled($statusWijzigingGeblokkeerd ?? false)
                            required
                        >
                            <option value="Niet Uitgereikt" @selected(old('status', is_null($pakket->datum_uitgifte) ? 'Niet Uitgereikt' : 'Uitgereikt') === 'Niet Uitgereikt')>
                                Niet Uitgereikt
                            </option>
                            <option value="Uitgereikt" @selected(old('status', is_null($pakket->datum_uitgifte) ? 'Niet Uitgereikt' : 'Uitgereikt') === 'Uitgereikt')>
                                Uitgereikt
                            </option>
                        </select>
                        <div class="invalid-feedback">Kies een geldige status.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" @disabled($statusWijzigingGeblokkeerd ?? false)>Wijzig status voedselpakket</button>
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
