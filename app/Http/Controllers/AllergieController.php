<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllergieOverzichtRequest;
use App\Models\Allergie;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AllergieController extends Controller
{
    public function index(AllergieOverzichtRequest $request): View
    {
        $allergieen = collect();
        $filters = [
            'klant_id' => $request->input('klant_id'),
            'zoekterm' => (string) $request->input('zoekterm', ''),
            'aantal_rijen' => (int) $request->input('aantal_rijen', 10),
        ];

        try {
            $gevalideerdeData = $request->validated();
            $klantId = isset($gevalideerdeData['klant_id']) ? (int) $gevalideerdeData['klant_id'] : null;
            $zoekterm = (string) ($gevalideerdeData['zoekterm'] ?? '');
            $aantalRijen = (int) ($gevalideerdeData['aantal_rijen'] ?? 10);
            $filters = [
                'klant_id' => $klantId,
                'zoekterm' => $zoekterm,
                'aantal_rijen' => $aantalRijen,
            ];

            $allergieen = Allergie::haalOverzichtViaStoredProcedure($klantId, $zoekterm, $aantalRijen);

            Log::info('Technische log: allergieenoverzicht geladen.', [
                'user_id' => $request->user()?->id,
                'klant_id_filter' => $klantId,
                'zoekterm' => $zoekterm === '' ? null : $zoekterm,
                'aantal_opgevraagd' => $aantalRijen,
                'aantal_resultaten' => $allergieen->count(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: allergieenoverzicht laden mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return view('allergieen.index', [
                'allergieen' => $allergieen,
                'filters' => $filters,
                'status_error' => 'Er is een storing. Daardoor kunnen allergieën nu niet worden geladen.',
            ]);
        }

        return view('allergieen.index', [
            'allergieen' => $allergieen,
            'filters' => $filters,
        ]);
    }
}
