<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllergieOverzichtRequest;
use App\Http\Requests\AllergieToevoegenRequest;
use App\Models\Allergie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AllergieController extends Controller
{
    public function store(AllergieToevoegenRequest $request): RedirectResponse
    {
        try {
            $allergieData = $request->validated();
            $klantId = (int) $allergieData['klant_id'];
            $resultaat = Allergie::voegToeViaStoredProcedure($klantId, (string) $allergieData['beschrijving']);

            if (! $resultaat['klant_bestaat']) {
                return back()
                    ->withInput()
                    ->with('status_error', 'De geselecteerde klant bestaat niet.');
            }

            if ($resultaat['toegevoegd']) {
                Log::info('Technische log: allergie succesvol toegevoegd.', [
                    'user_id' => $request->user()?->id,
                    'klant_id' => $klantId,
                    'allergie_id' => $resultaat['allergie_id'],
                    'beschrijving' => $allergieData['beschrijving'],
                ]);

                return redirect()
                    ->route('allergieen.index', ['klant_id' => $klantId])
                    ->with('status_success', 'De allergie is succesvol toegevoegd');
            }

            if ($resultaat['bestaat_gekoppeld']) {
                Log::warning('Technische log: allergie toevoegen geblokkeerd, al gekoppeld aan klant.', [
                    'user_id' => $request->user()?->id,
                    'klant_id' => $klantId,
                    'allergie_id' => $resultaat['allergie_id'],
                    'beschrijving' => $allergieData['beschrijving'],
                ]);

                return back()
                    ->withInput()
                    ->with('status_error', 'De allergie kan niet worden toegevoegd omdat een klant hier al allergisch voor is');
            }

            Log::warning('Technische log: allergie toevoegen niet uitgevoerd door onbekende oorzaak.', [
                'user_id' => $request->user()?->id,
                'klant_id' => $klantId,
                'allergie_id' => $resultaat['allergie_id'],
                'beschrijving' => $allergieData['beschrijving'],
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'De allergie kon niet worden toegevoegd.');
        } catch (Throwable $exception) {
            Log::error('Technische log: allergie toevoegen mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Er is een storing. De allergie kon niet worden toegevoegd.');
        }
    }

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
                'status_error' => 'Er is een storing. Daardoor kunnen allergieen nu niet worden geladen.',
            ]);
        }

        return view('allergieen.index', [
            'allergieen' => $allergieen,
            'filters' => $filters,
        ]);
    }
}
