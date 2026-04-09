@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Voorraadoverzicht</h1>
    </div>

    @if (!empty($melding))
        <div class="alert alert-warning" role="alert">
            <strong>Melding:</strong> {{ $melding }}
        </div>
    @endif

    @if (count($voorraad) > 0)
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Categorie</th>
                        <th>Aantal</th>
                        <th>Locatie</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($voorraad as $item)
                        <tr>
                            <td>{{ $item->product_naam }}</td>
                            <td>{{ $item->categorie_naam }}</td>
                            <td>{{ $item->hoeveelheid }}</td>
                            <td>{{ $item->locatie ?? '-' }}</td>
                            <td>
                                @if ($item->status === 'Leeg')
                                    <span class="badge text-bg-danger">Leeg</span>
                                @elseif ($item->status === 'Aanvullen')
                                    <span class="badge text-bg-warning">Aanvullen</span>
                                @else
                                    <span class="badge text-bg-success">{{ $item->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info mb-0" role="alert">
            Er is momenteel geen voorraad beschikbaar.
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Voorraadoverzicht</h1>
        @if (in_array(auth()->user()->role ?? null, ['directie', 'magazijn_medewerker'], true))
            <a href="{{ route('voorraad.create') }}" class="btn btn-success">Product toevoegen</a>
        @endif
    </div>
    @php $canManageVoorraad = in_array(auth()->user()->role ?? null, ['directie', 'magazijn_medewerker'], true); @endphp

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Controleer je invoer:</strong> {{ $errors->first() }}
        </div>
    @endif

    @if (!empty($melding))
        <div class="alert alert-warning" role="alert">
            <strong>Melding:</strong> {{ $melding }}
        </div>
    @endif

    @if (count($voorraad) > 0)
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Categorie</th>
                        <th>Aantal</th>
                        <th>Minimum</th>
                        <th>Locatie</th>
                        <th>Status</th>
                        @if ($canManageVoorraad)
                            <th>Actie</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($voorraad as $item)
                        <tr>
                            <td>{{ $item->product_naam }}</td>
                            <td>{{ $item->categorie_naam }}</td>
                            <td>{{ $item->hoeveelheid }}</td>
                            <td>{{ $item->minimum_voorraad }}</td>
                            <td>{{ $item->locatie ?? '-' }}</td>
                            <td>
                                @if ($item->status === 'Leeg')
                                    <span class="badge text-bg-danger">Leeg</span>
                                @elseif ($item->status === 'Aanvullen')
                                    <span class="badge text-bg-warning">Aanvullen</span>
                                @else
                                    <span class="badge text-bg-success">{{ $item->status }}</span>
                                @endif
                            </td>
                            @if ($canManageVoorraad)
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('voorraad.edit', $item->product_id) }}" class="btn btn-sm btn-primary">Wijzigen</a>
                                        <form method="POST" action="{{ route('voorraad.destroy', $item->product_id) }}" onsubmit="return confirm('Weet je zeker dat je dit product uit de voorraad wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Verwijderen</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info mb-0" role="alert">
            Er is momenteel geen voorraad beschikbaar.
        </div>
    @endif
</div>
@endsection
