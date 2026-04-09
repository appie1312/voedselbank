@extends('layouts.app')

@section('title', 'Accounts Overzicht')

@section('content')
    <div class="mb-3">
        <h1 class="h3 mb-1">Accounts Overzicht</h1>
<<<<<<< HEAD
        <p class="text-secondary mb-0"></p>
=======
        <p class="text-secondary mb-0">Alle gebruikers voor directie, met gegevens via INNER JOIN.</p>
>>>>>>> 49b7a9e1d147f3414d7c2a98dea1b09a3d7ceca6
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Telefoon</th>
                            <th>Afdeling</th>
                            <th>Aangemaakt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accounts as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($account->role)) }}</td>
                                <td>{{ $account->telefoon ?: '-' }}</td>
                                <td>{{ $account->afdeling ?: '-' }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($account->created_at)->format('d-m-Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Geen accounts beschikbaar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
