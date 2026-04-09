@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Voorraadoverzicht</h1>
<!-- resources/views/voorraad/index.blade.php -->
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Voorraadoverzicht - Voedselbank Maaskantje</title>
    <style>
        .alert { padding: 15px; background-color: #f8d7da; color: #721c24; margin-bottom: 20px; }
        .warning { background-color: #fff3cd; color: #856404; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>


    {{-- Feedback melding aan eindgebruiker --}}
    @if (!empty($melding))
        <div class="alert">
            <p><strong>Melding:</strong> {{ $melding }}</p>
        </div>
    @endif

    @if (count($voorraad) > 0)
        <table>
            <thead>
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
                    <tr class="{{ $item->status == 'Aanvullen' ? 'warning' : '' }}">
                        <td>{{ $item->product_naam }}</td>
                        <td>{{ $item->categorie_naam }}</td>
                        <td>{{ $item->hoeveelheid }}</td>
                        <td>{{ $item->locatie }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>

@endsection
