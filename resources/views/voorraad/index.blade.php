@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Voorraadoverzicht</h1>

    @if (!empty($melding))
        <div style="padding: 15px; background-color: #f8d7da; color: #721c24; margin-bottom: 20px; border-radius: 6px;">
            <strong>Melding:</strong> {{ $melding }}
        </div>
    @endif

    @if (count($voorraad) > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Product</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Categorie</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Aantal</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Locatie</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($voorraad as $item)
                    <tr style="{{ $item->status === 'Leeg' ? 'background-color:#f8d7da;' : ($item->status === 'Aanvullen' ? 'background-color:#fff3cd;' : '') }}">
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->product_naam }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->categorie_naam }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->hoeveelheid }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->locatie ?? '-' }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
