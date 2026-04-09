<?php

namespace App\Http\Controllers;

use App\Http\Requests\KlantOverzichtRequest;
use App\Http\Requests\KlantToevoegenRequest;
use App\Http\Requests\KlantVerwijderenRequest;
use App\Http\Requests\KlantWijzigenRequest;
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

    public function edit(int $klantId): View|RedirectResponse
    {
        try {
            $klant = Klant::haalKlantVoorWijzigen($klantId);

            if (! $klant) {
                return redirect()
                    ->route('klanten.index')
                    ->with('status_error', 'Deze klant kon niet worden gevonden.');
            }

            return view('klanten.edit', [
                'klant' => $klant,
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: formulier voor klant wijzigen laden mislukt.', [
                'user_id' => auth()->id(),
                'klant_id' => $klantId,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('status_error', 'Er is een storing. Het wijzigformulier kon niet worden geladen.');
        }
    }

    public function update(KlantWijzigenRequest $request, int $klantId): RedirectResponse
    {
        try {
            $klantData = $request->validated();
            $resultaat = Klant::wijzigViaStoredProcedure($klantId, $klantData);

            if (! $resultaat['klant_bestaat']) {
                return redirect()
                    ->route('klanten.index')
                    ->with('status_error', 'Deze klant kon niet worden gevonden.');
            }

            if ($resultaat['bestaat_email_al']) {
                Log::warning('Technische log: klant wijzigen geblokkeerd door bestaand e-mailadres.', [
                    'user_id' => $request->user()?->id,
                    'klant_id' => $klantId,
                    'email_nieuw' => $klantData['emailadres'] ?? null,
                ]);

                return back()
                    ->withInput()
                    ->with('status_error', 'De klantgegevens kunnen niet worden gewijzigd, want deze email bestaat al');
            }

            Log::info('Technische log: klant succesvol gewijzigd.', [
                'user_id' => $request->user()?->id,
                'klant_id' => $klantId,
                'gezinsnaam_nieuw' => $klantData['gezinsnaam'],
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('status_success', 'De klantgegevens zijn succesvol gewijzigd');
        } catch (Throwable $exception) {
            Log::error('Technische log: klant wijzigen mislukt.', [
                'user_id' => $request->user()?->id,
                'klant_id' => $klantId,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Er is een storing. De klantgegevens konden niet worden gewijzigd.');
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

    public function destroy(KlantVerwijderenRequest $request, int $klantId): RedirectResponse
    {
        try {
            $request->validated();
            $resultaat = Klant::verwijderViaStoredProcedure($klantId);

            if (! $resultaat['klant_bestaat']) {
                return redirect()
                    ->route('klanten.index')
                    ->with('status_error', 'Deze klant kon niet worden gevonden.');
            }

            if ($resultaat['aanwezig']) {
                Log::warning('Technische log: klant verwijderen geblokkeerd wegens aanwezigheid binnen land.', [
                    'user_id' => $request->user()?->id,
                    'klant_id' => $klantId,
                ]);

                return redirect()
                    ->route('klanten.index')
                    ->with('status_error', 'De klant kan niet worden verwijderd omdat hij/zij aanwezig is');
            }

            Log::info('Technische log: klant succesvol verwijderd.', [
                'user_id' => $request->user()?->id,
                'klant_id' => $klantId,
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('status_success', 'De klant is succesvol verwijderd');
        } catch (Throwable $exception) {
            Log::error('Technische log: klant verwijderen mislukt.', [
                'user_id' => $request->user()?->id,
                'klant_id' => $klantId,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('klanten.index')
                ->with('status_error', 'Er is een storing. De klant kon niet worden verwijderd.');
        }
    }
}
