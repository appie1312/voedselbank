@extends('layouts.app')

@section('title', 'Directie Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Directie Dashboard</h1>
            <p class="text-secondary mb-0">Overzicht van medewerkers en rollen binnen de voedselbank.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Totaal accounts</h2>
                    <p class="display-6 mb-0">{{ $accounts->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Directie</h2>
                    <p class="display-6 mb-0">{{ $accounts->where('role', 'directie')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Magazijn</h2>
                    <p class="display-6 mb-0">{{ $accounts->where('role', 'magazijn_medewerker')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h6 text-secondary">Vrijwilligers</h2>
                    <p class="display-6 mb-0">{{ $accounts->where('role', 'vrijwilliger')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="h5 mb-3">Gebruikersoverzicht (INNER JOIN users + user_profiles)</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Telefoon</th>
                            <th>Afdeling</th>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Nog geen accounts gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
