<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voorraad Overzicht</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f8f9fa;
        }

        h1 {
            margin-bottom: 20px;
        }

        .message {
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffe69c;
            color: #664d03;
            border-radius: 8px;
            width: fit-content;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        .status-voldoende {
            color: green;
            font-weight: bold;
        }

        .status-aanvullen {
            color: orange;
            font-weight: bold;
        }

        .status-leeg {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Voorraad Overzicht</h1>

    {{-- Als er geen voorraad is, toon melding --}}
    @if($voorraadItems->isEmpty())
        <div class="message">
            Er is geen voorraad beschikbaar.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Categorie</th>
                    <th>Hoeveelheid</th>
                    <th>Minimum voorraad</th>
                    <th>Locatie</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voorraadItems as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->naam ?? 'Onbekend product' }}</td>
                        <td>{{ $item->product->categorie->naam ?? 'Geen categorie' }}</td>
                        <td>{{ $item->hoeveelheid }}</td>
                        <td>{{ $item->minimum_voorraad ?? '-' }}</td>
                        <td>{{ $item->locatie ?? '-' }}</td>
                        <td>
                            @if($item->voorraad_status === 'Voldoende')
                                <span class="status-voldoende">{{ $item->voorraad_status }}</span>
                            @elseif($item->voorraad_status === 'Aanvullen')
                                <span class="status-aanvullen">{{ $item->voorraad_status }}</span>
                            @else
                                <span class="status-leeg">{{ $item->voorraad_status }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
