<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlantOverzichtRequest;
use App\Models\Klant;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class KlantenController extends Controller
{
    public function index(KlantOverzichtRequest $request): View
    {
        $klanten = collect();
        $statusSuccess = null;
        $filters = [
            'zoekterm' => (string) $request->input('zoekterm', ''),
            'aantal_rijen' => (int) $request->input('aantal_rijen', 5),
        ];

        try {
            $gevalideerdeData = $request->validated();
            $zoekterm = (string) ($gevalideerdeData['zoekterm'] ?? '');
            $aantalRijen = (int) ($gevalideerdeData['aantal_rijen'] ?? 5);
            $filters = [
                'zoekterm' => $zoekterm,
                'aantal_rijen' => $aantalRijen,
            ];

            $klanten = Klant::haalOverzichtViaStoredProcedure($zoekterm, $aantalRijen);
            $statusSuccess = 'Klantenoverzicht is succesvol geladen.';

            Log::info('Technische log: directie klantenoverzicht geladen.', [
                'user_id' => $request->user()?->id,
                'zoekterm' => $zoekterm === '' ? null : $zoekterm,
                'aantal_opgevraagd' => $aantalRijen,
                'aantal_resultaten' => $klanten->count(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: klantenoverzicht laden mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return view('klanten.index', [
                'klanten' => $klanten,
                'filters' => $filters,
                'status_error' => 'Er is een storing. Daardoor worden er momenteel geen klanten getoond.',
            ]);
        }

        return view('klanten.index', [
            'klanten' => $klanten,
            'filters' => $filters,
            'status_success' => $statusSuccess,
        ]);
    }
}
