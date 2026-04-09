<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlantOverzichtRequest;
use App\Http\Requests\KlantToevoegenRequest;
use App\Models\Klant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class KlantenController extends Controller
{
    public function create(): View
    {
        return view('klanten.create');
    }

    public function store(KlantToevoegenRequest $request): RedirectResponse
    {
        try {
            $klantData = $request->validated();
            $resultaat = Klant::voegToeViaStoredProcedure($klantData);

            if ($resultaat['toegevoegd']) {
                Log::info('Technische log: klant succesvol toegevoegd.', [
                    'user_id' => $request->user()?->id,
                    'klant_id' => $resultaat['klant_id'],
                    'gezinsnaam' => $klantData['gezinsnaam'],
                ]);

                return redirect()
                    ->route('klanten.index')
                    ->with('status_success', 'De klant is succesvol toegevoegd');
            }

            Log::warning('Technische log: klant niet toegevoegd omdat deze al bestaat.', [
                'user_id' => $request->user()?->id,
                'gezinsnaam' => $klantData['gezinsnaam'],
                'telefoonnummer' => $klantData['telefoonnummer'],
                'emailadres' => $klantData['emailadres'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'De klant is niet toegevoegd omdat deze al bestaat');
        } catch (Throwable $exception) {
            Log::error('Technische log: klant toevoegen mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Er is een storing. De klant kon niet worden toegevoegd.');
        }
    }

    public function index(KlantOverzichtRequest $request): View
    {
        $klanten = collect();
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
        ]);
    }
}
