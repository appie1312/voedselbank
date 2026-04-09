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
