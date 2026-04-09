@extends('layouts.app')

@section('title', 'Technische Logs | Directie')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1">Technische Logs</h1>
            <p class="text-secondary mb-0">Laatste 200 regels uit <code>{{ $logPath }}</code>.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('directie.dashboard') }}">Terug naar directie dashboard</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Tijd</th>
                            <th>Level</th>
                            <th>Bericht</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr>
                                <td>{{ $entry['tijd'] }}</td>
                                <td>
                                    @php
                                        $badgeClass = match ($entry['level']) {
                                            'info' => 'text-bg-info',
                                            'warning' => 'text-bg-warning',
                                            'error' => 'text-bg-danger',
                                            default => 'text-bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ strtoupper($entry['level']) }}</span>
                                </td>
                                <td class="font-monospace small" style="white-space: pre-wrap; word-break: break-word;">{{ $entry['bericht'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Nog geen technische logs gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
