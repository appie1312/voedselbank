@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="mb-0">Leveranciers</h1>
        <a href="{{ route('leveranciers.create') }}" class="btn btn-primary">Nieuwe leverancier</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Bedrijfsnaam</th>
                    <th>Adres</th>
                    <th>Telefoon</th>
                    <th>Contactpersoon</th>
                    <th>Email Contactpersoon</th>
                    <th>Volgende levering</th>
                    <th>Producten</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leveranciers as $leverancier)
                    <tr>
                        <td>{{ $leverancier->id }}</td>
                        <td>{{ $leverancier->bedrijfsnaam }}</td>
                        <td>{{ $leverancier->adres ?? '-' }}</td>
                        <td>{{ $leverancier->telefoonnummer ?? '-' }}</td>
                        <td>{{ $leverancier->contactpersoon_naam ?? '-' }}</td>
                        <td>{{ $leverancier->contactpersoon_email ?? '-' }}</td>
                        <td>{{ $leverancier->volgende_levering ? \Carbon\Carbon::parse($leverancier->volgende_levering)->format('d-m-Y H:i') : '-' }}</td>
                        <td>
                            @if(!empty($leverancier->producten) && $leverancier->producten->isNotEmpty())
                                <ul class="mb-0 ps-3">
                                    @foreach($leverancier->producten as $product)
                                        <li>{{ $product->productnaam }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Geen leveranciers gevonden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
